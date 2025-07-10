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
    public partial class AdminForos : System.Web.UI.Page
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
                ENPublicacion en = new ENPublicacion();
                DataSet d = new DataSet();
                d = en.listarClientesD();
                GridView1.DataSource = d;
                GridView1.DataBind();
            }

        }
        protected void Leer(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            if (RequiredForoId.IsValid)
            {
                ENForo en = new ENForo(int.Parse(TId_F.Text), "blank");
                bool result = en.readForo();
                if (result == false)
                    LResultado_F.Text = "Error en la lectura del foro";
                else
                {
                    TNombre.Text = en.nombre;
                    LResultado_F.Text = "Proceso de lectura realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
        }
        protected void LeerPrimero(object sender, EventArgs e)
        {
            ENForo en = new ENForo();
            bool result = en.readFirstForo();
            if (result == false)
                LResultado_F.Text = "Error en la lectura del primer foro";
            else
            {
                TId_F.Text = en.id.ToString();
                TNombre.Text = en.nombre;
                LResultado_F.Text = "Proceso de lectura de primer foro realizado con éxito";
            }
        }
        protected void LeerAnterior(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            if (RequiredForoId.IsValid)
            {
                ENForo en = new ENForo(int.Parse(TId_F.Text), "blank");
                bool result = en.readPrevForo();
                if (result == false)
                    LResultado_F.Text = "Error en la lectura del anterior foro";
                else
                {
                    TId_F.Text = en.id.ToString();
                    TNombre.Text = en.nombre;
                    LResultado_F.Text = "Proceso de lectura de anterior foro realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
        }
        protected void LeerSiguiente(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            if (RequiredForoId.IsValid)
            {
                ENForo en = new ENForo(int.Parse(TId_F.Text), "blank");
                bool result = en.readNextForo();
                if (result == false)
                    LResultado_F.Text = "Error en la lectura del siguiente foro";
                else
                {
                    TId_F.Text = en.id.ToString();
                    TNombre.Text = en.nombre;
                    LResultado_F.Text = "Proceso de lectura de siguiente foro realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
        }
        protected void Crear(object sender, EventArgs e)
        {
            RequiredForoNombre.Enabled = true;
            if (RequiredForoNombre.IsValid)
            {
                ENForo en = new ENForo(0, TNombre.Text);
                bool result = en.createForo();
                if (result == false)
                    LResultado_F.Text = "Error en la creación del foro";
                else
                    LResultado_F.Text = "Proceso de creación realizado con éxito";
            }
            RequiredForoNombre.Enabled = false;
        }
        protected void Actualizar(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid)
            {
                ENForo en = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                bool result = en.updateForo();
                if (result == false)
                    LResultado_F.Text = "Error en la actualización del foro";
                else
                    LResultado_F.Text = "Proceso de actualización realizado con éxito";
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
        }
        protected void Borrar(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            if (RequiredForoId.IsValid)
            {
                ENForo en = new ENForo(int.Parse(TId_F.Text), "blank");
                bool result = en.deleteForo();
                if (result == false)
                    LResultado_F.Text = "Error en el borrado del foro";
                else
                    LResultado_F.Text = "Proceso de borrado realizado con éxito";
            }
            RequiredForoId.Enabled = false;
        }
        protected void LeerT(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema en = new ENTema(int.Parse(TId_T.Text), "blank", foro);
                bool result = en.readTema();
                if (result == false)
                    LResultado_T.Text = "Error en la lectura del tema";
                else
                {
                    TTitulo.Text = en.titulo;
                    LResultado_T.Text = "Proceso de lectura realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
        }
        protected void LeerPrimeroT(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema en = new ENTema(0, "blank", foro);
                bool result = en.readFirstTema();
                if (result == false)
                    LResultado_T.Text = "Error en la lectura del primer tema";
                else
                {
                    TId_T.Text = en.id.ToString();
                    TTitulo.Text = en.titulo;
                    LResultado_T.Text = "Proceso de lectura de primer tema realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
        }
        protected void LeerAnteriorT(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema en = new ENTema(int.Parse(TId_T.Text), "blank", foro);
                bool result = en.readPrevTema();
                if (result == false)
                    LResultado_T.Text = "Error en la lectura del anterior tema";
                else
                {
                    TId_T.Text = en.id.ToString();
                    TTitulo.Text = en.titulo;
                    LResultado_T.Text = "Proceso de lectura de anterior tema realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
        }
        protected void LeerSiguienteT(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema en = new ENTema(int.Parse(TId_T.Text), "blank", foro);
                bool result = en.readNextTema();
                if (result == false)
                    LResultado_T.Text = "Error en la lectura del siguiente tema";
                else
                {
                    TId_T.Text = en.id.ToString();
                    TTitulo.Text = en.titulo;
                    LResultado_T.Text = "Proceso de lectura de siguiente tema realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
        }
        protected void CrearT(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaTitulo.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema en = new ENTema(0, TTitulo.Text, foro);
                bool result = en.createTema();
                if (result == false)
                    LResultado_T.Text = "Error en la creación del tema";
                else
                    LResultado_T.Text = "Proceso de creación realizado con éxito";
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
        }
        protected void ActualizarT(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid && RequiredTemaTitulo.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema en = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                bool result = en.updateTema();
                if (result == false)
                    LResultado_T.Text = "Error en la actualización del tema";
                else
                    LResultado_T.Text = "Proceso de actualización realizado con éxito";
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
        }
        protected void BorrarT(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema en = new ENTema(int.Parse(TId_T.Text), "blank", foro);
                bool result = en.deleteTema();
                if (result == false)
                    LResultado_T.Text = "Error en el borrado del tema";
                else
                    LResultado_T.Text = "Proceso de borrado realizado con éxito";
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
        }
        protected void LeerP(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            RequiredPubliId.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid
                && RequiredTemaTitulo.IsValid && RequiredPubliId.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema tema = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                ENUsuario usu = new ENUsuario();
                ENPublicacion en = new ENPublicacion(int.Parse(TId_P.Text), "blank", tema, usu);
                bool result = en.readPublicacion();
                if (result == false)
                    LResultado_P.Text = "Error en la lectura de la publicación";
                else
                {
                    TTexto.Text = en.text;
                    LResultado_P.Text = "Proceso de lectura realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
            RequiredPubliId.Enabled = false;
        }
        protected void LeerPrimeroP(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid
                && RequiredTemaTitulo.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema tema = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                ENUsuario usu = new ENUsuario();
                ENPublicacion en = new ENPublicacion(0, "blank", tema, usu);
                bool result = en.readFirstPublicacion();
                if (result == false)
                    LResultado_P.Text = "Error en la lectura de la primera publicación";
                else
                {
                    TId_P.Text = en.id.ToString();
                    TTexto.Text = en.text;
                    TUsuario.Text = en.usuario.id.ToString();
                    LResultado_P.Text = "Proceso de lectura de primera publicación realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
        }
        protected void LeerAnteriorP(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            RequiredPubliId.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid
                && RequiredTemaTitulo.IsValid && RequiredPubliId.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema tema = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                ENUsuario usu = new ENUsuario();
                ENPublicacion en = new ENPublicacion(int.Parse(TId_P.Text), "blank", tema, usu);
                bool result = en.readPrevPublicacion();
                if (result == false)
                    LResultado_P.Text = "Error en la lectura de la anterior publicación";
                else
                {
                    TId_P.Text = en.id.ToString();
                    TTexto.Text = en.text;
                    TUsuario.Text = en.usuario.id.ToString();
                    LResultado_P.Text = "Proceso de lectura de anterior publicación realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
            RequiredPubliId.Enabled = false;
        }
        protected void LeerSiguienteP(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            RequiredPubliId.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid
                && RequiredTemaTitulo.IsValid && RequiredPubliId.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema tema = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                ENUsuario usu = new ENUsuario();
                ENPublicacion en = new ENPublicacion(int.Parse(TId_P.Text), "blank", tema, usu);
                bool result = en.readNextPublicacion();
                if (result == false)
                    LResultado_P.Text = "Error en la lectura de la siguiente publicación";
                else
                {
                    TId_P.Text = en.id.ToString();
                    TTexto.Text = en.text;
                    TUsuario.Text = en.usuario.id.ToString();
                    LResultado_P.Text = "Proceso de lectura de siguiente publicación realizado con éxito";
                }
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
            RequiredPubliId.Enabled = false;
        }
        protected void CrearP(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            RequiredPubliTexto.Enabled = true;
            RequiredUsuario.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid
                && RequiredTemaTitulo.IsValid && RequiredPubliTexto.IsValid && RequiredUsuario.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema tema = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                ENUsuario usu = new ENUsuario();
                usu.id = int.Parse(TUsuario.Text);
                ENPublicacion en = new ENPublicacion(0, TTexto.Text, tema, usu);
                bool result = en.createPublicacion();
                if (result == false)
                    LResultado_P.Text = "Error en la creación de la publicación";
                else
                    LResultado_P.Text = "Proceso de creación realizado con éxito";
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
            RequiredPubliTexto.Enabled = false;
            RequiredUsuario.Enabled = false;
        }
        protected void ActualizarP(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            RequiredPubliId.Enabled = true;
            RequiredPubliTexto.Enabled = true;
            RequiredUsuario.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid
                && RequiredTemaTitulo.IsValid && RequiredPubliId.IsValid && RequiredPubliTexto.IsValid && RequiredUsuario.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema tema = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                ENUsuario usu = new ENUsuario();
                ENPublicacion en = new ENPublicacion(int.Parse(TId_P.Text), TTexto.Text, tema, usu);
                bool result = en.updatePublicacion();
                if (result == false)
                    LResultado_P.Text = "Error en la actualización de la publicación";
                else
                    LResultado_P.Text = "Proceso de actualización realizado con éxito";
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
            RequiredPubliId.Enabled = false;
            RequiredPubliTexto.Enabled = false;
            RequiredUsuario.Enabled = false;
        }
        protected void BorrarP(object sender, EventArgs e)
        {
            RequiredForoId.Enabled = true;
            RequiredForoNombre.Enabled = true;
            RequiredTemaId.Enabled = true;
            RequiredTemaTitulo.Enabled = true;
            RequiredPubliId.Enabled = true;
            RequiredUsuario.Enabled = true;
            if (RequiredForoId.IsValid && RequiredForoNombre.IsValid && RequiredTemaId.IsValid
                && RequiredTemaTitulo.IsValid && RequiredPubliId.IsValid && RequiredUsuario.IsValid)
            {
                ENForo foro = new ENForo(int.Parse(TId_F.Text), TNombre.Text);
                ENTema tema = new ENTema(int.Parse(TId_T.Text), TTitulo.Text, foro);
                ENUsuario usu = new ENUsuario();
                ENPublicacion en = new ENPublicacion(int.Parse(TId_P.Text), "blank", tema, usu);
                bool result = en.deletePublicacion();
                if (result == false)
                    LResultado_P.Text = "Error en el borrado de la publicación";
                else
                    LResultado_P.Text = "Proceso de borrado realizado con éxito";
            }
            RequiredForoId.Enabled = false;
            RequiredForoNombre.Enabled = false;
            RequiredTemaId.Enabled = false;
            RequiredTemaTitulo.Enabled = false;
            RequiredPubliId.Enabled = false;
            RequiredUsuario.Enabled = false;
        }
        protected void Gridview1_SelectedItemChanged(object sender, EventArgs e)
        {
            TId_P.Text = GridView1.SelectedRow.Cells[1].Text;
            TTexto.Text = GridView1.SelectedRow.Cells[2].Text;
            TUsuario.Text = GridView1.SelectedRow.Cells[3].Text;
            TId_T.Text = GridView1.SelectedRow.Cells[4].Text;
            TTitulo.Text = GridView1.SelectedRow.Cells[5].Text;
            TId_F.Text = GridView1.SelectedRow.Cells[6].Text;
            TNombre.Text = GridView1.SelectedRow.Cells[7].Text;
        }
        protected void ChangePage(object sender, GridViewPageEventArgs e)
        {
            GridView1.PageIndex = e.NewPageIndex;
            ENPublicacion en = new ENPublicacion();
            DataSet d = new DataSet();
            d = en.listarClientesD();
            GridView1.DataSource = d;
            GridView1.DataBind();
        }
    }
}