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
    public partial class Lista_Deseos : System.Web.UI.Page
    {
        private ENUsuario usu = new ENUsuario();
        protected void Page_Load(object sender, EventArgs e)
        {
            if (Session["login_nick"] != null)
            {
                usu.nick = Session["login_nick"].ToString();
                usu.readUsuario();
            }
            else
                Response.Redirect("Inicia_Sesion.aspx");
            if (!Page.IsPostBack)
            {
                ENLista_Deseos en = new ENLista_Deseos();
                en.usuario = usu;
                DataSet d = new DataSet();
                d = en.listarVjLista();
                GridView1.DataSource = d;
                GridView1.DataBind();
            }
        }
        protected void Gridview1_SelectedItemDeleted(object sender, GridViewDeleteEventArgs e)
        {
            ENVideojuego vj = new ENVideojuego();
            vj.Titulo = GridView1.Rows[e.RowIndex].Cells[1].Text;
            vj.readVideojuego();
            ENLista_Deseos lista = new ENLista_Deseos();
            lista.deleteVjLista(vj);
        }

        protected void BRefresh_Click(object sender, EventArgs e)
        {
            ENLista_Deseos en = new ENLista_Deseos();
            en.usuario = usu;
            DataSet d = new DataSet();
            d = en.listarVjLista();
            GridView1.DataSource = d;
            GridView1.DataBind();
        }
        protected void ChangePage(object sender, GridViewPageEventArgs e)
        {
            GridView1.PageIndex = e.NewPageIndex;
            ENLista_Deseos en = new ENLista_Deseos();
            en.usuario = usu;
            DataSet d = new DataSet();
            d = en.listarVjLista();
            GridView1.DataSource = d;
            GridView1.DataBind();
        }
    }
}