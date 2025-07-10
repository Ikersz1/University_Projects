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
    public partial class AdminUsuarios : System.Web.UI.Page
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
                ENUsuario en = new ENUsuario();
                DataSet d = new DataSet();
                d = en.listarClientesD();
                GridView1.DataSource = d;
                GridView1.DataBind();
            }
        }
        protected void Leer(object sender, EventArgs e)
        {
            RequiredNick.Enabled = true;
            if (Page.IsValid)
            {
                ENUsuario en = new ENUsuario(0, TNick.Text, "blank", "blank", "blank", "blank", System.DateTime.Now, "blank", false);
                bool result = en.readUsuario();
                if (result == false)
                    LResultado.Text = "Error en la lectura del usuario";
                else
                {
                    TId.Text = en.id.ToString();
                    TNombre.Text = en.nombre;
                    TApellidos.Text = en.apellidos;
                    TEmail.Text = en.email;
                    TTelefono.Text = en.telef;
                    TFecha.Text = en.fecha_nac.ToString();
                    TRol.Text = en.admin.ToString();
                    TPassword.Text = en.password;
                    LResultado.Text = "Proceso de lectura realizado con éxito";
                }
            }
            RequiredNick.Enabled = false;
        }
        protected void LeerPrimero(object sender, EventArgs e)
        {
            ENUsuario en = new ENUsuario();
            bool result = en.readFirstUsuario();
            if (result == false)
                LResultado.Text = "Error en la lectura del primera usuario";
            else
            {
                TId.Text = en.id.ToString();
                TNick.Text = en.nick;
                TNombre.Text = en.nombre;
                TApellidos.Text = en.apellidos;
                TEmail.Text = en.email;
                TTelefono.Text = en.telef;
                TFecha.Text = en.fecha_nac.ToString();
                TRol.Text = en.admin.ToString();
                TPassword.Text = en.password;
                LResultado.Text = "Proceso de lectura de primer usuario realizado con éxito";
            }
        }
        protected void LeerAnterior(object sender, EventArgs e)
        {
            RequiredNick.Enabled = true;
            if (Page.IsValid)
            {
                ENUsuario en = new ENUsuario(0, TNick.Text, "blank", "blank", "blank", "blank", System.DateTime.Now, "blank", false);
                bool result = en.readPrevUsuario();
                if (result == false)
                    LResultado.Text = "Error en la lectura del anterior usuario";
                else
                {
                    TId.Text = en.id.ToString();
                    TNick.Text = en.nick;
                    TNombre.Text = en.nombre;
                    TApellidos.Text = en.apellidos;
                    TEmail.Text = en.email;
                    TTelefono.Text = en.telef;
                    TFecha.Text = en.fecha_nac.ToString();
                    TRol.Text = en.admin.ToString();
                    TPassword.Text = en.password;
                    LResultado.Text = "Proceso de lectura de anterior usuario realizado con éxito";
                }
            }
            RequiredNick.Enabled = false;
        }
        protected void LeerSiguiente(object sender, EventArgs e)
        {
            RequiredNick.Enabled = true;
            if (Page.IsValid)
            {
                ENUsuario en = new ENUsuario(0, TNick.Text, "blank", "blank", "blank", "blank", System.DateTime.Now, "blank", false);
                bool result = en.readNextUsuario();
                if (result == false)
                    LResultado.Text = "Error en la lectura del siguiente usuario";
                else
                {
                    TId.Text = en.id.ToString();
                    TNick.Text = en.nick;
                    TNombre.Text = en.nombre;
                    TApellidos.Text = en.apellidos;
                    TEmail.Text = en.email;
                    TTelefono.Text = en.telef;
                    TFecha.Text = en.fecha_nac.ToString();
                    TRol.Text = en.admin.ToString();
                    TPassword.Text = en.password;
                    LResultado.Text = "Proceso de lectura de siguiente usuario realizado con éxito";
                }
            }
            RequiredNick.Enabled = false;
        }
        protected void Crear(object sender, EventArgs e)
        {
            RequiredNick.Enabled = true;
            RequiredNombre.Enabled = true;
            RequiredApellidos.Enabled = true;
            RequiredEmail.Enabled = true;
            RequiredPassword.Enabled = true;
            RequiredFecha.Enabled = true;
            RequiredTelefono.Enabled = true;
            RequiredRol.Enabled = true;
            if (Page.IsValid)
            {
                ENUsuario en = new ENUsuario(0, TNick.Text, TNombre.Text, TApellidos.Text, TEmail.Text, TPassword.Text, Convert.ToDateTime(TFecha.Text), TTelefono.Text, Convert.ToBoolean(TRol.Text));
                bool result = en.createUsuario();
                if (result == false)
                    LResultado.Text = "Error en la creación del usuario";
                else
                    LResultado.Text = "Proceso de creación realizado con éxito";
            }
            RequiredNick.Enabled = false;
            RequiredNombre.Enabled = false;
            RequiredApellidos.Enabled = false;
            RequiredEmail.Enabled = false;
            RequiredPassword.Enabled = false;
            RequiredFecha.Enabled = false;
            RequiredTelefono.Enabled = false;
            RequiredRol.Enabled = false;
        }
        protected void Actualizar(object sender, EventArgs e)
        {
            RequiredNick.Enabled = true;
            RequiredNombre.Enabled = true;
            RequiredApellidos.Enabled = true;
            RequiredEmail.Enabled = true;
            RequiredPassword.Enabled = true;
            RequiredFecha.Enabled = true;
            RequiredTelefono.Enabled = true;
            RequiredRol.Enabled = true;
            if (Page.IsValid)
            {
                ENUsuario en = new ENUsuario(0, TNick.Text, TNombre.Text, TApellidos.Text, TEmail.Text, TPassword.Text, Convert.ToDateTime(TFecha.Text), TTelefono.Text, Convert.ToBoolean(TRol.Text));
                bool result = en.updateUsuario();
                if (result == false)
                    LResultado.Text = "Error en la actualización del usuario";
                else
                    LResultado.Text = "Proceso de actualización realizado con éxito";
            }
            RequiredNick.Enabled = false;
            RequiredNombre.Enabled = false;
            RequiredApellidos.Enabled = false;
            RequiredEmail.Enabled = false;
            RequiredPassword.Enabled = false;
            RequiredFecha.Enabled = false;
            RequiredTelefono.Enabled = false;
            RequiredRol.Enabled = false;
        }
        protected void Borrar(object sender, EventArgs e)
        {
            RequiredNick.Enabled = true;
            if (Page.IsValid)
            {
                ENUsuario en = new ENUsuario(0, TNick.Text, "blank", "blank", "blank", "blank", System.DateTime.Now, "blank", false);
                bool result = en.deleteUsuario();
                if (result == false)
                    LResultado.Text = "Error en el borrado del usuario";
                else
                    LResultado.Text = "Proceso de borrado realizado con éxito";
            }
            RequiredNick.Enabled = false;
        }
        protected void Gridview1_SelectedItemChanged(object sender, EventArgs e)
        {
            ENUsuario usu = new ENUsuario();
            usu.nick = GridView1.SelectedRow.Cells[2].Text;
            usu.readUsuario();
            TId.Text = usu.id.ToString();
            TNick.Text = usu.nick;
            TNombre.Text = usu.nombre;
            TApellidos.Text = usu.apellidos;
            TEmail.Text = usu.email;
            TTelefono.Text = usu.telef;
            TPassword.Text = usu.password;
            TFecha.Text = Convert.ToString(usu.fecha_nac);
            TRol.Text = usu.admin.ToString();
        }
        protected void ChangePage(object sender, GridViewPageEventArgs e)
        {
            GridView1.PageIndex = e.NewPageIndex;
            ENUsuario en = new ENUsuario();
            DataSet d = new DataSet();
            d = en.listarClientesD();
            GridView1.DataSource = d;
            GridView1.DataBind();
        }
    }
}