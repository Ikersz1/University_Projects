using System;
using System.Collections.Generic;
using System.Deployment.Internal;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using library;

namespace web
{
    public partial class Videojuego : System.Web.UI.Page
    {
        protected String EmailSession;
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!String.IsNullOrEmpty(Request.QueryString["id"]))
            {
                // Query string value is there so now use it
                int id = Convert.ToInt32(Request.QueryString["id"]);
                ENVideojuego videojuego = new ENVideojuego();
                videojuego.Id = id;
                if (videojuego.readVideojuegoId())
                {
                    //Display data
                    videojuegoImagen.ImageUrl = videojuego.Imagen;
                    tituloLabel.Text = videojuego.Titulo;
                    productoraLink.Text = videojuego.Productora.Nombre;
                    categoriaLink.Text = videojuego.Categoria.nombre;
                    fechaLabel.Text = videojuego.FechaLanzamiento.ToString();
                    plataformaLabel.Text = videojuego.Plataforma;
                    descripcionLabel.Text = videojuego.Descripcion;
                    precioLabel.Text = videojuego.Precio.ToString();
                    fillReviews(videojuego.Id);
                    fillOferta(videojuego.Id);
                    comprobarSesionReview();

                    reservas.Visible = videojuego.FechaLanzamiento > DateTime.Now ? true : false;
                }
            }
        }

        protected void clickAddList(object sender, EventArgs e)
        {
            if (Session["login_nick"] == null)
            {
                Response.Redirect("Inicia_Sesion.aspx");
            }
            else
            {
                ENVideojuego videojuego = new ENVideojuego();
                videojuego.Id = Int32.Parse(((ImageButton)sender).CommandArgument);
                if (videojuego.readVideojuegoId())
                {
                    //Se agrega a lista de deseos
                    ENLista_Deseos lista = new ENLista_Deseos();
                    ENUsuario auxUser = new ENUsuario();
                    auxUser.nick = Session["login_nick"].ToString();
                    auxUser.readUsuario();
                    lista.usuario = auxUser;
                    lista.readListaPorUsu();
                    //Agregar elemento a la lista
                    lista.addVideojuegoLista(videojuego.Id);
                }
            }
        }

        protected void clickAddCart(object sender, EventArgs e)
        {
            if (Session["login_nick"] == null)
            {
                Response.Redirect("Inicia_Sesion.aspx");
            }
            else
            {
                ENVideojuego videojuego = new ENVideojuego();
                videojuego.Id = Int32.Parse(((ImageButton)sender).CommandArgument);
                if (videojuego.readVideojuegoId())
                {
                    //Se agrega a lista de deseos
                    ENCesta cesta = new ENCesta();
                    ENUsuario auxUser = new ENUsuario();
                    auxUser.nick = Session["login_nick"].ToString();
                    //Se lee usuario
                    auxUser.readUsuario();
                    cesta.usuarioID = auxUser;
                    cesta.readCesta();
                    //Agregar elemento a la lista
                    cesta.addVideojuego(videojuego.Id);
                }
            }
        }

        protected void fillReviews(int videojuego)
        {
            ENReview review = new ENReview();
            review.videojuego.Id = videojuego;

            string columnaOrden = ordenar.SelectedValue.ToString();
            listViewReviews.DataSource = review.filtrarReview(columnaOrden);
            listViewReviews.DataBind();
        }

        protected void fillOferta(int videojuego)
        {
            ENOferta oferta = new ENOferta();
            ofertaDisplay.DataSource = oferta.readOferta(videojuego);
            ofertaDisplay.DataBind();
            //Comprobar si contenido en oferta
            if (ofertaDisplay.Items.Count > 0)
            {
                precioLabel.Style.Add("text-decoration", "line-through");
                //Actualizar precio
            }
        }

        protected void crearReview_click(object sender, EventArgs e)
        {
            ENReview review = new ENReview();

            //Lectura del usuario de la sesion
            review.usuario.nick = Session["login_nick"].ToString();
            review.usuario.readUsuario();

            //Id de la sesion
            review.videojuego.Id = Convert.ToInt32(Request.QueryString["id"]);
            review.comentario = comentarioReview.Text.ToString();
            review.puntuacion = Convert.ToInt32(puntuacionReview.SelectedValue);
            review.createReview();

            fillReviews(review.videojuego.Id);
            recargarPagina(Convert.ToInt32(Request.QueryString["id"]));
        }

        //Funcion con la cual se visualizan los campos correspondientes para la inserción de una review
        protected void añadirReview_Click(object sender, EventArgs e)
        {
            //Inicio de sesion comprobado antes
            textoPuntuacion.Visible = true;
            comentarioReview.Visible = true;
            puntuacionReview.Visible = true;
            crearReview.Visible = true;
            cancelar.Visible = true;
            añadirReview.Visible = false;
            reservas.Visible = false;
        }

        protected void comprobarSesionReview()
        {
            if (Session["login_nick"] != null)
            {
                añadirReview.Visible = true;
                reservas.Style["display"] = "block";
            }
            else
            {
                añadirReview.Visible = false;
                reservas.Style["display"] = "none";
            }

        }

        protected void cancelarReview_click(object sender, EventArgs e)
        {
            recargarPagina(Convert.ToInt32(Request.QueryString["id"]));
        }

        protected void filtrarReview_click(Object sender, EventArgs e)
        {
            //Como en la llamada principal llama por defecto al filtrar Reviews , no hace nada 
        }

        //Función privada para recargar la pagina de videojuego
        private void recargarPagina(int id)
        {
            string url = "Videojuego.aspx?id=" + id;
            Response.Redirect(url);
        }
        protected void ReservarVideojuego_Click(object sender, EventArgs e)
        {
            ENReserva reserva = new ENReserva();

            //Datos usuario
            reserva.usuario.nick = Session["login_nick"].ToString();
            reserva.usuario.readUsuario();

            //Datos Videojuego
            reserva.videojuego.Id = Convert.ToInt32(Request.QueryString["id"]);
            reserva.videojuego.readVideojuegoId();
            //Codigo a revisar
            //if (reserva.existeReserva())
            //{
            //    reservaCreadaValidator.Visible = true;        
            //}
            //else
            //{
                reserva.createReserva();
            //}
            
        }
    }
}