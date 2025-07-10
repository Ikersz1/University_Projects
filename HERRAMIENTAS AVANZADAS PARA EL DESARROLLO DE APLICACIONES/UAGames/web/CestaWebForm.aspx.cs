using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using library;

namespace web
{
        public partial class CestaWebForm : System.Web.UI.Page
        {
        // Aquí indicar el id de la base de datos por la que empezará, revisad que no haya saltos
        public static int idCabecera = 1;

            DataTable data = new DataTable();
            protected void Page_Load(object sender, EventArgs e)
            {

                DataTable data = new DataTable();
                if (!Page.IsPostBack)
                {
                    if (Session["login_nick"] == null)
                    {
                        Response.Redirect("Inicia_Sesion.aspx");
                    }

                    FillCestasTable();
                    decimal precioTotal = 0;

                    // Recorre los elementos del GridView y suma el precio de cada uno
                    foreach (GridViewRow row in cestaTable.Rows)
                    {
                        precioTotal += Convert.ToDecimal(row.Cells[5].Text);
                    }

                    // Muestra el precio total en un Label
                    precioTotalLabel.Text = precioTotal.ToString("C");

                    cestaTable.Columns[0].Visible = false;
                    cestaTable.Columns[1].Visible = false;

                 }
            }

            protected void changePageCestaTable(object sender, GridViewPageEventArgs e)
            {
                cestaTable.PageIndex = e.NewPageIndex;
                FillCestasTable();
            }

            protected void FillCestasTable()
            {
                ENUsuario usuario = new ENUsuario();
                usuario.nick = (Session["login_nick"]).ToString();
                usuario.readUsuario();
                ENCesta cesta = new ENCesta();
                cesta.usuarioID.id = usuario.id;
                
                data = cesta.readCestas();
                cestaTable.DataSource = data;
                cestaTable.DataBind();               
            }
            protected void clickRowEditCesta(object sender, GridViewEditEventArgs e)
            {
                cestaTable.EditIndex = e.NewEditIndex;
                FillCestasTable();
            }

            protected void clickRowCancelCesta(object sender, GridViewCancelEditEventArgs e)
            {
                //Setting the EditIndex property to -1 to cancel the Edit mode in Gridview  
                cestaTable.EditIndex = -1;
                FillCestasTable();
            }

            protected void clickRowUpdateCesta(object sender, GridViewUpdateEventArgs e)
            {

            }

            protected void clickRowDeleteCesta(object sender, GridViewDeleteEventArgs e)
            {
                ENCesta en = new ENCesta();
                en.usuarioID.id = Int32.Parse(cestaTable.Rows[e.RowIndex].Cells[0].Text);
                en.videojuegoID.Id = Int32.Parse(cestaTable.Rows[e.RowIndex].Cells[1].Text);
                bool result = en.deleteCesta();

            if (result == false)
            {
                Resultado.Text = "Error en el borrado de la categoria";
            }
            else
            {
                FillCestasTable();
                Resultado.Text = "Proceso de borrado realizado con éxito";
            }
            }

            protected void ComprarClick(object sender, EventArgs e)
            {
            if (Session["login_nick"] == null)
            {
                Response.Redirect("Inicia_Sesion.aspx");
            }
            else
            {
                ENUsuario usuario = new ENUsuario();
                usuario.nick = Session["login_nick"].ToString();
                usuario.readUsuario();
                DateTime fecha = DateTime.Now;
                double precioTotal = 0.00;
                foreach (GridViewRow row in cestaTable.Rows)
                {
                    String precio = row.Cells[5].Text;
                    precioTotal += Convert.ToDouble(precio);
                };

                ENCabecera_Compra compra = new ENCabecera_Compra(usuario, fecha, precioTotal);
                compra.createCabecera_Compra();
                // Falta que recorra todos los productos, actualmente sólo podemos hacer pedido de 1 producto y una unidad.
                int cantidad = 1;
                foreach (GridViewRow row in cestaTable.Rows)
                {
                    if (row.RowType == DataControlRowType.DataRow)
                    {
                        double importe = Convert.ToDouble(row.Cells[4].Text);
                        ENVideojuego videojuego = new ENVideojuego();
                        ENCesta cesta = new ENCesta();
                        videojuego.Id = cesta.articulosCesta(usuario);
                        ENLinea_Compra linea = new ENLinea_Compra(cantidad, importe, videojuego);
                        linea.createLinea_Compra(CestaWebForm.idCabecera);
                        CestaWebForm.idCabecera++;
                    }
                }
                Response.Redirect("ThankYouPage.aspx");
            }
            }
        }
}

