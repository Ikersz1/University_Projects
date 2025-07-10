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
    public partial class Catalogo : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!Page.IsPostBack)
            {
                fillCatalogue();
                FillCategoriasDropdown();
                FillProductorasDropdown();
            }
        }

        protected void fillCatalogue()
        {
            ENVideojuego videojuego = new ENVideojuego();
            VideojuegosListView.DataSource = videojuego.readVideojuegos();
            VideojuegosListView.DataBind();
        }

        protected void filtrarOnClick(object sender, EventArgs e)
        {
            int productora = int.Parse(filtroProductora.SelectedValue);
            int categoria = int.Parse(filtroCategoria.SelectedValue);
            string orden = ordenar.SelectedValue;

            string query = "WHERE ";
            //Se van agregando filtros a la query
            if (productora != 0)
            {
                query += " v.productoraID = '" + productora + "' AND";
            }

            if (categoria != 0)
            {
                query += " v.categoriaID = '" + categoria + "' AND";
            }

            string titulo = filtroTitulo.Text.ToString();

            if (titulo != "")
            {
                query += " titulo LIKE '%" + titulo + "%' AND";
            }

            if (filtroFecha.Text.ToString() != "")
            {
                DateTime fecha = DateTime.Parse(filtroFecha.Text.ToString()).Date;
                query += " fecha_lanzamiento >= '" + fecha + "' AND";

            }

            string plataforma = filtroPlataforma.Text.ToString();

            if (plataforma != "")
            {
                query += " plataforma LIKE '%" + plataforma + "%' AND";
            }

            int precio = int.Parse(filtroPrecio.Text.ToString());
            query += " precio <= '" + precio + "' ORDER BY '" + orden + "';";


            ENVideojuego videojuego = new ENVideojuego();          
            VideojuegosListView.DataSource = videojuego.readVideojuegos(query);
            VideojuegosListView.DataBind();
        }

        protected void resetFiltrosOnClick(object sender, EventArgs e)
        {
            fillCatalogue();
            filtroProductora.SelectedValue = "0";
            filtroCategoria.SelectedValue = "0";
            filtroTitulo.Text = "";
            filtroPlataforma.Text = "";
            ordenar.SelectedValue = "id";
        }

        protected void FillProductorasDropdown()
        {
            ENProductora productora = new ENProductora();
            DataTable dt = productora.readProductorasNombre();
            ListItem i;
            foreach (DataRow r in dt.Rows)
            {
                i = new ListItem(r["nombre"].ToString(), r["id"].ToString());
                filtroProductora.Items.Add(i);
            }
        }

        protected void FillCategoriasDropdown()
        {
            ENCategoria categoria = new ENCategoria();
            DataTable dt = categoria.readCategoriasNombre();
            ListItem i;
            foreach (DataRow r in dt.Rows)
            {
                i = new ListItem(r["nombre"].ToString(), r["id"].ToString());
                filtroCategoria.Items.Add(i);
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

    }
}