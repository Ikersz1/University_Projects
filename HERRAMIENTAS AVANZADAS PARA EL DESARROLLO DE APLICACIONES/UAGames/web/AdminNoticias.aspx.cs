using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data.SqlClient;
using library;
using System.Data;

namespace web
{
    public partial class AdminNoticias : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {

        }
        protected void onCrear(object sender, EventArgs e)
        {
            if (txtImg.Text != "" && txtTitulo.Text != "" && txtContenido.Text != "" )
            {
                ENNoticia noti = new ENNoticia();
                noti.Imagen = txtImg.Text;
                noti.Titulo = txtTitulo.Text;
                noti.Contenido = txtContenido.Text;

                noti.ProductoraID = int.Parse(txtprodID.Text);
                

                if (noti.createNoticia())
                {
                    PResultado.Text = "Noticia  insertada en la B.D.";
                }
                else PResultado.Text = "No es posible insertar la noticia.";
            }

            else PResultado.Text = "Alguno de los campos no estan especificados.";
        }
        protected void onLeerTitulo(object sender, EventArgs e)
        {
            PResultado.Text = "";
            if (txtTitulo.Text == "")
                PResultado.Text = "Titulo de la noticia no introducido.";
            else
            {
                ENNoticia noti = new ENNoticia();
                noti.Titulo = txtTitulo.Text;
                DataTable dt = new DataTable();

                dt = noti.readNoticiasTitulo2();

                if (dt.Rows.Count == 0)
                {
                    PResultado.Text = "No existe una noticiaen la base de datos con ese Titulo";
                }
                else
                {
                    ListView1.DataSource = dt;
                    ListView1.DataBind();
                }

            }

        }
        protected void onLeerTodas(object sender, EventArgs e)
        {
            PResultado.Text = "";


            ENNoticia noti = new ENNoticia();
            noti.Titulo = txtTitulo.Text;
            DataTable dt = new DataTable();

            dt = noti.readNoticias();

            if (dt.Rows.Count == 0)
            {
                PResultado.Text = "No hay noticias en la base de datos";
            }
            else
            {
                ListView1.DataSource = dt;
                ListView1.DataBind();
            }



        }
        protected void onLeerId(object sender, EventArgs e)
        {
            PResultado.Text = "";
            if (txtId.Text == "")
                PResultado.Text = "ID de la noticia no introducido.";
            else
            {
                ENNoticia noti = new ENNoticia();
                noti.Id = int.Parse(txtId.Text);

                DataTable dt = new DataTable();

                dt = noti.readNoticiasId2();

                if (dt.Rows.Count == 0)
                {
                    PResultado.Text = "No existe una noticia en la base de datos con ese ID " + txtId.Text;
                }
                else
                {
                    ListView1.DataSource = dt;
                    ListView1.DataBind();
                }

            }

        }
        protected void onBorrar(object sender, EventArgs e)
        {
            if (txtId.Text != "")
            {
                ENNoticia noti = new ENNoticia();
                noti.Id = int.Parse(txtId.Text);
                noti.Titulo = txtTitulo.Text;


                if (noti.deleteNoticia())
                {

                    PResultado.Text = "noticia " + noti.Id + " borrada con éxito";
                }


                else PResultado.Text = "No es posible borrar la noticia";

            }

            else PResultado.Text = "Alguno de los campos no estan especificados.";
        }
        protected void onUpdate(object sender, EventArgs e)
        {
            if (txtId.Text != "" && txtTitulo.Text != "" && txtContenido.Text != "" && txtImg.Text != "" && txtFecha.Text != "")
            {
                ENNoticia noti = new ENNoticia();
                noti.Titulo = txtTitulo.Text;
                noti.Contenido = txtContenido.Text;
                noti.Imagen = txtImg.Text;
                noti.FechaPublicacion = DateTime.Parse(txtFecha.Text);
                noti.Id = int.Parse(txtId.Text);

                if (noti.updateNoticia())
                {
                    PResultado.Text = "Noticia " + noti.Id + " actualizada con exito.";
                }
                else PResultado.Text = "Esta Noticia no existe en la B.D.";
            }

            else PResultado.Text = "Alguno de los campos no estan especificados.";

        }

    }


}
