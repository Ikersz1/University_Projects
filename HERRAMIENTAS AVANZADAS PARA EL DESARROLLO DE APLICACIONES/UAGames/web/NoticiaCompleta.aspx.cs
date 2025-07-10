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
    public partial class NoticiaCompleta : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!IsPostBack)
            {
                if (Request.QueryString["id"] != null)
                {
                    string idNoticia = Request.QueryString["id"];
                    ENNoticia noti = new ENNoticia();
                    noti.Id = int.Parse(idNoticia);

                    DataTable dt = new DataTable();

                    dt = noti.readNoticiasId2();

                   
                    
                        ListView1.DataSource = dt;
                        ListView1.DataBind();
                    
                }
            }

        }
    }
}