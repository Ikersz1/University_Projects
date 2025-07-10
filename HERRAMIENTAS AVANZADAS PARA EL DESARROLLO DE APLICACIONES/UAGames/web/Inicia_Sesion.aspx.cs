using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using library;

namespace web
{
    public partial class Inicia_Sesion : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {

        }
        protected void Leer(object sender, EventArgs e)
        {
            RequiredNick.Enabled = true;
            RequiredPassword.Enabled = true;
            if (Page.IsValid) { 
                ENUsuario en = new ENUsuario(0, TNick.Text, "blank", "blank", "blank", TPassword.Text, System.DateTime.Now, "blank", false);
                bool result = en.readUsuario();
                if (result == false)
                    LResultado.Text = "Usuario o contraseña incorrectos";
                else
                {
                    if (TPassword.Text != en.password)
                        LResultado.Text = "Usuario o contraseña incorrectos";
                    else
                    {
                        Session["login_nick"] = TNick.Text;
                        Response.Redirect("Inicio.aspx");
                    }
                }
            }
            RequiredNick.Enabled = false;
            RequiredPassword.Enabled = false;
        }
    }
}