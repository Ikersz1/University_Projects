using library;
using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;

namespace web
{
    public partial class Foro_Publico : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if(Session["login_nick"] == null)
            {
                Response.Redirect("Inicia_Sesion.aspx");
            }
            ENForo f = new ENForo();
            DataSet foros = new DataSet();
            foros = f.readAllForo();
            DataTable t = new DataTable();
            t = foros.Tables["Foro"];
            for(int i = 0; i < t.Rows.Count; i++)
            {
                DForo.Items.Add(t.Rows[i][0].ToString());
            }
        }
        protected void BuscarTemas(object sender, EventArgs e)
        {
            ENTema t = new ENTema();
            DataSet foros = new DataSet();
            foros = t.readAllTema(DForo.SelectedItem.Text);
            DataTable tab = new DataTable();
            tab = foros.Tables["Tema"];
            for (int i = 0; i < tab.Rows.Count; i++)
            {
                DTema.Items.Add(tab.Rows[i][0].ToString());
            }
            DTema.Visible = true;
            LTema.Visible = true;
            BBuscar.Visible = true;
        }
        protected void Buscar(object sender, EventArgs e)
        {
            ENPublicacion en = new ENPublicacion();
            DataSet d = new DataSet();
            string tema = DTema.SelectedItem.Text;
            d = en.listarClientesDPublico(tema);
            GridView1.DataSource = d;
            GridView1.DataBind();
        }
        protected void Publicar(object sender, EventArgs e)
        {
            ENTema tema = new ENTema();
            tema.titulo = DTema.SelectedItem.Text;
            tema.readTemaTitulo();
            ENUsuario usu = new ENUsuario();
            usu.nick = Session["login_nick"].ToString();
            usu.readUsuario();
            ENPublicacion en = new ENPublicacion(0, TTexto.Text, tema, usu);
            bool result = en.createPublicacion();
            if (result == false)
                LResultado.Text = "Error en la publicación";
            else
                LResultado.Text = "Proceso de publicación realizado con éxito";
        }
        protected void ChangePage(object sender, GridViewPageEventArgs e)
        {
            GridView1.PageIndex = e.NewPageIndex;
            ENPublicacion en = new ENPublicacion();
            DataSet d = new DataSet();
            string tema = DTema.SelectedItem.Text;
            d = en.listarClientesDPublico(tema);
            GridView1.DataSource = d;
            GridView1.DataBind();
        }
    }
}