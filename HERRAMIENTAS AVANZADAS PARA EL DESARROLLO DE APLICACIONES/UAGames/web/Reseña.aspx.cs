using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.DynamicData;
using System.Web.UI;
using System.Web.UI.WebControls;
using library;

namespace web
{
    public partial class Reseña : System.Web.UI.Page
    {
        DataTable data = new DataTable();
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!Page.IsPostBack)
            {
                ENReview en = new ENReview();
                en.id = Convert.ToInt32(Request.QueryString["id"]);

                if (en.readReview())
                {
                    //Si el usuario de la sesion corresponde con el de la reseña , mostramos los campos de edición
                    if (Session["login_nick"]!=null && Session["login_nick"].ToString() == en.usuario.nick.ToString())
                    {
                        Editar.Visible = true;
                        Eliminar.Visible = true;
                    }
                    videojuegoImagen.ImageUrl = en.videojuego.Imagen;
                    tituloLabel.Text = en.videojuego.Titulo + " - ";
                    fechaLabel.Text = en.fecha.ToString("dd/MM/yyyy");
                    Puntuacion.CurrentRating = Convert.ToInt32(en.puntuacion);
                    UsuarioLabel.Text = en.usuario.nombre.ToString();
                    comentarioLabel.Text = en.comentario.ToString();
                }

            }
        }

        

        public void Volver_click(object sender, EventArgs e)
        {
            //Cargamos los datos de la review para volver al videojuego en el que nos encontrabamos
            ENReview en = new ENReview();
            en.id = Convert.ToInt32(Request.QueryString["id"]);
            en.readReview();
            recargarPagina("Videojuego", en.videojuego.Id);
        }

        public void Editar_click(object sender, EventArgs e)
        {
            textoPuntuacion.Visible = true;
            puntuacionReview.Visible = true;
            comentarioReview.Visible = true;
            confirmar.Visible = true;
            cancelar.Visible = true;
        }

        public void Eliminar_click(object sender, EventArgs e)
        {
            int idVideojuego;
            ENReview en = new ENReview();
            int idReseña = Convert.ToInt32(Request.QueryString["id"]);

            //Cargamos datos de la review
            en.id = idReseña;
            en.readReview();

            //Guardamos la Id del videojuego para asi poder volver a la pagina anterior
            idVideojuego = en.videojuego.Id;
            en.deleteReview();
            recargarPagina("Videojuego", idVideojuego);
        }

        public void Confirmar_click(object sender, EventArgs e)
        {
            //Cargamos los datos
            ENReview en = new ENReview();
            en.id = Convert.ToInt32(Request.QueryString["id"]);
            en.readReview();

            //Updateamos lo necesarios
            en.comentario = comentarioReview.Text;
            en.puntuacion = Convert.ToInt32(puntuacionReview.SelectedValue);
            en.updateReview();
            recargarPagina("Reseña", en.id);
        }

        public void Cancelar_click(object sender, EventArgs e)
        {
            recargarPagina("Reseña", Convert.ToInt32(Request.QueryString["id"]));
        }

        private void recargarPagina(string pagina , int id = -1)
        {
            string url = id != -1 ? (pagina + ".aspx?id=" + id) : (pagina + ".aspx");
            Response.Redirect(url);
        }
    }
}