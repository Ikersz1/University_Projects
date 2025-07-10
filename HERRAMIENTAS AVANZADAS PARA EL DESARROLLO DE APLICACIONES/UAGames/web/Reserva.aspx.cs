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
    public partial class Reserva : System.Web.UI.Page
    {
        DataTable data = new DataTable();
        protected void Page_Load(object sender, EventArgs e)
        {
            ENReserva en = new ENReserva();
            en.usuario.nick = Session["login_nick"].ToString();
            en.usuario.readUsuario();

            data = en.mostrarReservas();
            pagarReserva.Visible = data.Rows.Count == 0 ? false : true; 
            reservaTable.DataSource = data;
            reservaTable.DataBind();
        }

        protected DataTable mostrarReservas()
        {
            ENReserva r = new ENReserva();
            return r.mostrarReservas();
        }

        
        protected void changePageReservaTable(object sender, GridViewPageEventArgs e)
        {
            reservaTable.PageIndex = e.NewPageIndex;
            mostrarReservas();
        }

        protected void clickRowEditReserva(object sender, GridViewEditEventArgs e)
        {
        }

        protected void clickRowCancelReserva(object sender, GridViewCancelEditEventArgs e)
        { 
            reservaTable.EditIndex = -1;
            mostrarReservas();
        }

        protected void clickRowUpdateReserva(object sender, GridViewUpdateEventArgs e)
        {

        }

        protected void clickRowDeleteReserva(object sender, GridViewDeleteEventArgs e)
        {
            ENReserva en = new ENReserva();
            en.videojuego.Titulo = reservaTable.Rows[e.RowIndex].Cells[1].Text.ToString();
            bool result = en.deleteReserva();

            recargarPagina("Reserva");

        }

        protected void mostrarPagar_click(object sender, EventArgs e)
        {
            nombreJuego.Visible = true;
            pagar.Visible = true;
            cancelar.Visible = true;
            confirmar.Visible = true;
            pagarReserva.Visible = false;
        }

        public void Confirmar_click(object sender, EventArgs e)
        {
            //Cargamos los datos
            ENReserva en = new ENReserva();

            en.videojuego.Titulo = nombreJuego.Text.ToString();
            en.pagado = Convert.ToInt32(pagar.Text.ToString());
            en.updateReserva();

            //Updateamos lo necesarios
            recargarPagina("Reserva");
        }

        public void Cancelar_click(object sender, EventArgs e)
        {
            recargarPagina("Reserva");
        }

        private void recargarPagina(string pagina, int id = -1)
        {
            string url = id != -1 ? (pagina + ".aspx?id=" + id) : (pagina + ".aspx");
            Response.Redirect(url);
        }
    }
}
