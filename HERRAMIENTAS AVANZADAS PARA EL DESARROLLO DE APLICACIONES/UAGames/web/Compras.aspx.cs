using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using library;

namespace web
{
    public partial class Compras : System.Web.UI.Page
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
            

        }
    }
}