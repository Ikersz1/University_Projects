using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using library;

namespace web
{
    public partial class Registrar : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {

        }
        protected void Crear(object sender, EventArgs e)
        {
            if (Page.IsValid)
            {
                if (TPassword.Text != TRepitePassword.Text)
                    LResultado.Text = "Las contraseñas introducidas no coinciden";
                else
                {
                    ENUsuario en = new ENUsuario(0, TNick.Text, TNombre.Text, TApellidos.Text, TEmail.Text, TPassword.Text, Convert.ToDateTime(TFecha.Text), TTelefono.Text, false);
                    bool result = en.createUsuario();
                    en.readUsuario();
                    if (result == false)
                        LResultado.Text = "Error en la creación del usuario";
                    else
                    {
                        ENLista_Deseos lista = new ENLista_Deseos();
                        lista.nombre = "Lista de " + en.nick;
                        lista.descripcion = "Lista de deseos de " + en.nombre + " " + en.apellidos;
                        lista.usuario = en;
                        lista.createLista();
                        Session["login_nick"] = en.nick;
                        Response.Redirect("Inicio.aspx");
                    }
                }
            }
        }
    }
}