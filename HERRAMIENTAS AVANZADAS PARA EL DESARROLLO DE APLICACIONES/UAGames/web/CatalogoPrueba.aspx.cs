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
    public partial class CatalogoPrueba : System.Web.UI.Page
    {

        protected void Page_Load(object sender, EventArgs e)
        {
            if (!Page.IsPostBack && VideojuegosListView != null)
            {
                ENVideojuego videojuego = new ENVideojuego();
                VideojuegosListView.DataSource = videojuego.readVideojuegos();
                VideojuegosListView.DataBind();
            }

        }

        protected void clickProductora(object sender, EventArgs e)
        {

        }
        protected void clickCategoria(object sender, EventArgs e)
        {

        }
        protected void clickPlataforma(object sender, EventArgs e)
        {

        }
        protected void clickAddToListaDeseos(object sender, EventArgs e)
        {

        }

        protected void clickSeeReviews(object sender, EventArgs e)
        {

        }
    }
}