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
    public partial class AdminOfertas : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if (Session["login_nick"] == null)
                Response.Redirect("Inicia_Sesion.aspx");
            ENUsuario usuario = new ENUsuario();
            usuario.nick = Session["login_nick"].ToString();
            usuario.readUsuario();
            if (!usuario.admin)
                Response.Redirect("Inicia_Sesion.aspx");
            if (!Page.IsPostBack)
            {
                FillOfertasTable();
                FillProductorasDropdown();
                FillVideojuegosDropdown();
                cleanMsg();
            }
        }

        protected void changePageOfertasTable(object sender, GridViewPageEventArgs e)
        {
            cleanMsg();
            ofertasTable.PageIndex = e.NewPageIndex;
            FillOfertasTable();
        }

        protected void FillOfertasTable()
        {
            cleanMsg();
            ENOferta oferta = new ENOferta();
            //Persist the table in the Session object.
            Session["OfertasGrid"] = oferta.readOfertas();

            //Bind the GridView control to the data source.           
            ofertasTable.DataSource = Session["OfertasGrid"];
            ofertasTable.DataBind();
        }

        protected void OfertasTable_Sorting(object sender, GridViewSortEventArgs e)
        {
            cleanMsg();
            //Retrieve the table from the session object.
            DataTable dt = Session["OfertasGrid"] as DataTable;

            if (dt != null)
            {

                //Sort the data.
                dt.DefaultView.Sort = e.SortExpression + " " + GetSortDirection(e.SortExpression);
                ofertasTable.DataSource = Session["OfertasGrid"];
                ofertasTable.DataBind();
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
        protected void FillVideojuegosDropdown()
        {
            ENVideojuego videojuego = new ENVideojuego();
            DataTable dt = videojuego.readVideojuegos();
            ListItem i;
            foreach (DataRow r in dt.Rows)
            {
                i = new ListItem(r["titulo"].ToString(), r["id"].ToString());
                videojuegosList.Items.Add(i);
                filtroVideojuego.Items.Add(i);
            }
        }

        protected void FillVideojuegosDropdownOnChange()
        {
            videojuegosList.Items.Clear();
            //Se obtienen solo los de la productora
            int productora = int.Parse(productorasList.SelectedValue);
            ENVideojuego videojuego = new ENVideojuego();
            DataTable dt;

            if (productora != 0)
            {
                videojuego.Productora.Id = productora;
                dt = videojuego.readVideojuegosProductora();
            }
            else
            {
                dt = videojuego.readVideojuegos();
            }


            ListItem i;
            foreach (DataRow r in dt.Rows)
            {
                i = new ListItem(r["titulo"].ToString(), r["id"].ToString());
                videojuegosList.Items.Add(i);
            }
        }

        protected void FillFiltroVideojuegos()
        {
            filtroVideojuego.Items.Clear();
            //ToDo leer productora y obtener solo los de la productora
            int productora = int.Parse(filtroProductora.SelectedValue);
            ENVideojuego videojuego = new ENVideojuego();
            DataTable dt = new DataTable();

            if (productora != 0)
            {
                videojuego.Productora.Id = productora;
                dt = videojuego.readVideojuegosProductora();
            }
            else
            {
                dt = videojuego.readVideojuegos();
            }


            ListItem i;
            foreach (DataRow r in dt.Rows)
            {
                i = new ListItem(r["titulo"].ToString(), r["id"].ToString());
                filtroVideojuego.Items.Add(i);
            }
        }

        protected void clickRowEditOferta(object sender, GridViewEditEventArgs e)
        {
            cleanMsg();
            //NewEditIndex property used to determine the index of the row being edited.  
            ofertasTable.EditIndex = e.NewEditIndex;
            FillOfertasTable();
        }

        protected void clickRowCancelOferta(object sender, GridViewCancelEditEventArgs e)
        {
            cleanMsg();
            //Setting the EditIndex property to -1 to cancel the Edit mode in Gridview  
            ofertasTable.EditIndex = -1;
            FillOfertasTable();
        }

        protected void clickRowUpdateOferta(object sender, GridViewUpdateEventArgs e)
        {
            cleanMsg();
            ENOferta oferta = new ENOferta();
            oferta.Id = Int32.Parse(ofertasTable.Rows[e.RowIndex].Cells[0].Text);
            oferta.Nombre = ((TextBox)ofertasTable.Rows[e.RowIndex].Cells[1].Controls[0]).Text;
            oferta.Videojuego.Titulo = ((TextBox)ofertasTable.Rows[e.RowIndex].Cells[2].Controls[0]).Text;
            oferta.Productora.Nombre = ((TextBox)ofertasTable.Rows[e.RowIndex].Cells[3].Controls[0]).Text;
            oferta.Descuento = int.Parse(((TextBox)ofertasTable.Rows[e.RowIndex].Cells[4].Controls[0]).Text);
            oferta.FechaInicio = DateTime.Parse(((TextBox)ofertasTable.Rows[e.RowIndex].Cells[5].Controls[0]).Text);
            oferta.FechaFin = DateTime.Parse(((TextBox)ofertasTable.Rows[e.RowIndex].Cells[6].Controls[0]).Text);


            if (oferta.updateOferta())
            {
                ofertasTable.EditIndex = -1;
                FillOfertasTable();
                //Mensaje mostrando exito
                mostrarResultado("Oferta actualizada correctamente.");
            }
            else
            {
                //Mensaje mostrando error
                mostrarError("Error al actualizar la oferta.");
            }
        }

        protected void clickRowDeleteOferta(object sender, GridViewDeleteEventArgs e)
        {
            cleanMsg();
            ENOferta oferta = new ENOferta();
            oferta.Id = Int32.Parse(ofertasTable.Rows[e.RowIndex].Cells[0].Text);
            oferta.Nombre = ofertasTable.Rows[e.RowIndex].Cells[1].Text;

            if (oferta.deleteOferta())
            {
                FillOfertasTable();
                //Mensaje mostrando exito
                mostrarResultado("Oferta eliminada correctamente.");
            }
            else
            {
                //Mensaje mostrando error
                mostrarError("Error al eliminar la oferta.");
            }
        }

        protected void ProductoraSelectionChange(object sender, EventArgs e)
        {
            //POner videojeugos de productora exclusivamente
            FillVideojuegosDropdownOnChange();
        }

        protected void filtroProductoraOnChange(object sender, EventArgs e)
        {
            //POner videojeugos de productora exclusivamente
            FillFiltroVideojuegos();
        }

        protected void crearOfertaClick(object sender, EventArgs e)
        {
            if (productorasList.SelectedValue == "0" || videojuegosList.SelectedValue == "0")
            {
                msgValidar.Text = "Por favor, selecciona una productora y una categoria.";
            }
            else if (DateTime.Parse(nuevaFechaFin.Text.ToString()) < DateTime.Parse(nuevaFechaInicio.Text.ToString()))
            {
                msgValidar.Text = "La fecha de fin de la oferta debe ser mayor que la fecha de inicio.";
            }
            else
            {
                ENOferta en = new ENOferta();
                en.Nombre = nuevoNombre.Text.ToString();
                en.Descuento = Int32.Parse(nuevoDescuento.Text.ToString());
                en.FechaInicio = DateTime.Parse(nuevaFechaInicio.Text.ToString());
                en.FechaFin = DateTime.Parse(nuevaFechaFin.Text.ToString());
                en.Productora.Id = Int32.Parse(productorasList.SelectedValue);
                en.Videojuego.Id = Int32.Parse(videojuegosList.SelectedValue);


                if (en.readOferta())
                {
                    msgValidar.Text = "ERROR: Ya existe una oferta con ese nombre.";
                }
                else if(en.addOferta())
                {
                    msgSalidaCrear.Text = "Oferta creada correctamente.";
                    msgSalidaCrear.BackColor = System.Drawing.Color.Green;
                    msgSalidaCrear.ForeColor = System.Drawing.Color.White;
                    FillOfertasTable();
                }
                else
                {
                    msgSalidaCrear.Text = "ERROR al crear la oferta.";
                    msgSalidaCrear.BackColor = System.Drawing.Color.Red;
                    msgSalidaCrear.ForeColor = System.Drawing.Color.White;
                }

            }
        }

        protected void filtrarOfertasOnClick(object sender, EventArgs e)
        {
            int productora = int.Parse(filtroProductora.SelectedValue);
            int videojuego = int.Parse(filtroVideojuego.SelectedValue);

            string query = "WHERE";
            //Se van agregando filtros a la query
            if(productora != 0)
            {
                query += " o.productoraID = '" + productora + "' AND";
            }

            if (videojuego != 0)
            {
                query += " o.videojuegoID = '" + videojuego + "' AND";
            }

            string nombre = filtroNombre.Text.ToString();

            if(nombre != "")
            {
                query += " o.nombre LIKE '%" + nombre + "%' AND";
            }

            if(fechaInicio.Text.ToString() != "")
            {
                DateTime inicio = DateTime.Parse(fechaInicio.Text.ToString()).Date;
                query += " o.fecha_inicio >= '" + inicio.ToString("yyyy/MM/dd") + "' AND";

            }

            if (fechaFin.Text.ToString() != "")
            {
                DateTime fin = DateTime.Parse(fechaFin.Text.ToString());
                query += " o.fecha_fin <= '" + fin.ToString("yyyy/MM/dd") + "' AND";

            }

            int descuento = int.Parse(filtroDescuento.Text.ToString());
            query += " o.descuento >= '" + descuento + "' ;";

            ENOferta oferta = new ENOferta();
            //Persist the table in the Session object.
            Session["OfertasGrid"] = oferta.readOfertas(query);

            //Bind the GridView control to the data source.           
            ofertasTable.DataSource = Session["OfertasGrid"];
            ofertasTable.DataBind();
        }

        protected void resetFiltrosOnClick(object sender, EventArgs e)
        {
            FillOfertasTable();
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