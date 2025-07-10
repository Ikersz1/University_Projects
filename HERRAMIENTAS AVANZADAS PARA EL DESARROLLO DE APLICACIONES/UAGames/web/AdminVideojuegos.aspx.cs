using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using library;

namespace web
{
    public partial class AdminVideojuegos : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if (Session["login_nick"] == null)
            {
                Response.Redirect("Inicia_Sesion.aspx");
            }
                
            ENUsuario usuario = new ENUsuario();
            usuario.nick = Session["login_nick"].ToString();
            usuario.readUsuario();
            if (!usuario.admin)
            {
                Response.Redirect("Inicia_Sesion.aspx");
            }
               
            if (!Page.IsPostBack)
            {
                FillVideojuegosTable();
                FillProductorasDropdown();
                FillCategoriasDropdown();
                cleanMsg();
            }
        }

        protected void changePageVideojuegosTable(object sender, GridViewPageEventArgs e)
        {
            videojuegoTable.PageIndex = e.NewPageIndex;
            FillVideojuegosTable();
        }

        protected void FillVideojuegosTable()
        {
            ENVideojuego videojuego = new ENVideojuego();
            //Persist the table in the Session object.
            Session["VideojuegosGrid"] = videojuego.readVideojuegos();

            //Bind the GridView control to the data source.
            videojuegoTable.DataSource = Session["VideojuegosGrid"];
            videojuegoTable.DataBind();
        }

        protected void VideojuegosTable_Sorting(object sender, GridViewSortEventArgs e)
        {

            //Retrieve the table from the session object.
            DataTable dt = Session["VideojuegosGrid"] as DataTable;

            if (dt != null)
            {

                //Sort the data.
                dt.DefaultView.Sort = e.SortExpression + " " + GetSortDirection(e.SortExpression);
                videojuegoTable.DataSource = Session["VideojuegosGrid"];
                videojuegoTable.DataBind();
            }

        }

        private string GetSortDirection(string column)
        {

            // By default, set the sort direction to ascending.
            string sortDirection = "ASC";

            // Retrieve the last column that was sorted.
            string sortExpression = ViewState["SortExpression"] as string;

            if (sortExpression != null)
            {
                // Check if the same column is being sorted.
                // Otherwise, the default value can be returned.
                if (sortExpression == column)
                {
                    string lastDirection = ViewState["SortDirection"] as string;
                    if ((lastDirection != null) && (lastDirection == "ASC"))
                    {
                        sortDirection = "DESC";
                    }
                }
            }

            // Save new values in ViewState.
            ViewState["SortDirection"] = sortDirection;
            ViewState["SortExpression"] = column;

            return sortDirection;
        }

        protected void FillProductorasDropdown()
        {
            ENProductora productora = new ENProductora();
            DataTable dt = productora.readProductorasNombre();
            ListItem i;
            foreach (DataRow r in dt.Rows)
            {
                i = new ListItem(r["nombre"].ToString(), r["id"].ToString());
                productorasList.Items.Add(i);
                filtroProductora.Items.Add(i);
            }
        }

        protected void FillCategoriasDropdown()
        {
            ENCategoria categoria = new ENCategoria();
            DataTable dt = categoria.readCategoriasNombre();
            ListItem i;
            foreach (DataRow r in dt.Rows)
            {
                i = new ListItem(r["nombre"].ToString(), r["id"].ToString());
                categoriasList.Items.Add(i);
                filtroCategoria.Items.Add(i);
            }
        }

        protected void clickRowEditVideojuego(object sender, GridViewEditEventArgs e)
        {
            //NewEditIndex property used to determine the index of the row being edited.  
            videojuegoTable.EditIndex = e.NewEditIndex;
            FillVideojuegosTable();
        }

        protected void clickRowCancelVideojuego(object sender, GridViewCancelEditEventArgs e)
        {
            //Setting the EditIndex property to -1 to cancel the Edit mode in Gridview  
            videojuegoTable.EditIndex = -1;
            FillVideojuegosTable();
        }


        protected void clickRowUpdateVideojuego(object sender, GridViewUpdateEventArgs e)
        {
            ENVideojuego videojuego = new ENVideojuego();
            videojuego.Id = Int32.Parse(videojuegoTable.Rows[e.RowIndex].Cells[0].Text);
            videojuego.Titulo = ((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[1].Controls[0]).Text;
            //ToDo resolver actualizar productora  y categoria en videojuego
            videojuego.Productora.Nombre = ((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[2].Controls[0]).Text;

            videojuego.Categoria.nombre = ((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[3].Controls[0]).Text;

            videojuego.FechaLanzamiento = DateTime.Parse(((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[4].Controls[0]).Text);
            videojuego.Precio = Double.Parse(((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[5].Controls[0]).Text);
            videojuego.Plataforma = ((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[6].Controls[0]).Text;
            videojuego.Imagen = ((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[7].Controls[0]).Text;
            videojuego.Descripcion = ((TextBox)videojuegoTable.Rows[e.RowIndex].Cells[8].Controls[0]).Text;

            //videojuegoTable.DataSource = videojuego.updateVideojuego(videojuegoTable.SelectedIndex);
            //videojuegoTable.DataBind();

            if (videojuego.updateVideojuego())
            {
                videojuegoTable.EditIndex = -1;
                FillVideojuegosTable();
                //Mensaje mostrando exito
                mostrarResultado("Videojuego actualizado correctamente.");
            }
            else
            {
                //Mensaje mostrando error
                mostrarError("Error al actualizar el videojuego.");
            }

        }

        protected void clickRowDeleteVideojuego(object sender, GridViewDeleteEventArgs e)
        {
            ENVideojuego videojuego = new ENVideojuego();
            videojuego.Id = Int32.Parse(videojuegoTable.Rows[e.RowIndex].Cells[0].Text);
            videojuego.Titulo = videojuegoTable.Rows[e.RowIndex].Cells[1].Text;

            if (videojuego.deleteVideojuego())
            {
                FillVideojuegosTable();
                //Mensaje mostrando exito
                mostrarResultado("Videojuego eliminado correctamente.");
            }
            else
            {
                //Mensaje mostrando error
                mostrarError("Error al eliminar el videojuego.");
            }
        }

        protected void crearVideojuegoClick(object sender, EventArgs e)
        {
            
            if(productorasList.SelectedValue == "0" || categoriasList.SelectedValue == "0")
            {
                msgValidar.Text = "Por favor, selecciona una productora y una categoria.";
            }
            else
            {
                ENVideojuego en = new ENVideojuego();
                en.Titulo = nuevoTitulo.Text.ToString();
                en.Plataforma = nuevaPlataforma.Text.ToString();
                en.Descripcion = nuevaDescripcion.Text.ToString();
                en.FechaLanzamiento = DateTime.Parse(nuevaFechaLanzamiento.Text.ToString());
                en.Precio = Double.Parse(nuevoPrecio.Text.ToString());
                en.Productora.Id = Int32.Parse(productorasList.SelectedValue);
                en.Categoria.id = Int32.Parse(categoriasList.SelectedValue);

                if (en.readVideojuego())
                {
                    msgValidar.Text = "ERROR: Ya existe un videojuego con ese título.";
                } 
                else if (en.addVideojuego())
                {
                    msgSalidaCrear.Text = "Videojuego creado correctamente.";
                    msgSalidaCrear.BackColor = System.Drawing.Color.Green;
                    FillVideojuegosTable();
                }
                else
                {
                    msgSalidaCrear.Text = "ERROR al crear el videojuego.";
                    msgSalidaCrear.BackColor = System.Drawing.Color.Red;
                }

            }   
        }

        protected void filtrarOnClick(object sender, EventArgs e)
        {
            cleanMsg();
            int productora = int.Parse(filtroProductora.SelectedValue);
            int categoria = int.Parse(filtroCategoria.SelectedValue);

            string query = "WHERE ";
            //Se van agregando filtros a la query
            if (productora != 0)
            {
                query += " v.productoraID = '" + productora + "' AND";
            }

            if (categoria != 0)
            {
                query += " v.categoriaID = '" + categoria + "' AND";
            }

            string titulo = filtroTitulo.Text.ToString();

            if (titulo != "")
            {
                query += " titulo LIKE '%" + titulo + "%' AND";
            }

            if (filtroFecha.Text.ToString() != "")
            {
                DateTime fecha = DateTime.Parse(filtroFecha.Text.ToString()).Date;
                query += " fecha_lanzamiento >= '" + fecha.ToString("yyyy/MM/dd") + "' AND";

            }

            string plataforma = filtroPlataforma.Text.ToString();

            if (plataforma != "")
            {
                query += " plataforma LIKE '%" + plataforma + "%' AND";
            }

            int precio = int.Parse(filtroPrecio.Text.ToString());
            query += " precio >= '" + precio + "' ;";

            ENVideojuego videojuego = new ENVideojuego();
            //Persist the table in the Session object.
            Session["VideojuegosGrid"] = videojuego.readVideojuegos(query);

            //Bind the GridView control to the data source.           
            videojuegoTable.DataSource = Session["VideojuegosGrid"];
            videojuegoTable.DataBind();
        }

        protected void resetFiltrosOnClick(object sender, EventArgs e)
        {
            FillVideojuegosTable();
            filtroProductora.SelectedValue = "0";
            filtroCategoria.SelectedValue = "0";
            filtroTitulo.Text = "";
            filtroPlataforma.Text = "";
            cleanMsg();
        }


        private void mostrarError(string error)
        {
            msgSalidaCrear.Text = error;
            msgSalidaCrear.BackColor = System.Drawing.Color.Red;
        }

        private void cleanMsg()
        {
            msgSalidaCrear.Text = "";
            msgSalidaCrear.BackColor = System.Drawing.Color.White;
            msgValidar.Text = "";
        }

        private void mostrarResultado(string resultado)
        {
            msgSalidaCrear.Text = resultado;
            msgSalidaCrear.BackColor = System.Drawing.Color.Green;
        }



    }
}