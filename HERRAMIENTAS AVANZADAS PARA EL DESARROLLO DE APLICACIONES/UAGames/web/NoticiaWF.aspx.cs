using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using library;

namespace web
{
    public partial class NoticiaWF : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!IsPostBack)
            {
                BindData();
            }
        }

        protected void BindData()
        {
            SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["miconexion"].ToString());
            SqlDataAdapter sda = new SqlDataAdapter("SELECT Noticia.*, Productora.nombre AS nombre FROM Noticia INNER JOIN Productora ON Noticia.productoraID = Productora.ID", con);
            DataTable dt = new DataTable();
            sda.Fill(dt);
            ListView1.DataSource = dt;
            ListView1.DataBind();
        }

        protected void ListView1_ItemDataBound(object sender, ListViewItemEventArgs e)
        {
            if (e.Item.ItemType == ListViewItemType.DataItem)
            {
                ListViewDataItem dataItem = (ListViewDataItem)e.Item;

                // Obtener la referencia al control HtmlGenericControl dentro del ItemTemplate
                HtmlGenericControl divItem = (HtmlGenericControl)e.Item.FindControl("divItem");

                // Aplicar las clases CSS según el índice del elemento de datos
                if (dataItem.DisplayIndex == 0)
                {
                    divItem.Attributes["class"] = "list list-left";
                }
                else if (dataItem.DisplayIndex == 1)
                {
                    divItem.Attributes["class"] = "list list-right";
                }
                else
                {
                    divItem.Attributes["class"] = "list";
                }
            }
        }
    }
}
