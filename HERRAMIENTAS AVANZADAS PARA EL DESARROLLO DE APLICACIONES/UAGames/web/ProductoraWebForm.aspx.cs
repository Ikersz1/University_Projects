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
    public partial class ProductoraWebForm : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {

        }
        protected void onCrear(object sender, EventArgs e)
        {
            if (txtImg.Text != "" && txtNombre.Text != "" && txtDescripcion.Text != "" && txtWeb.Text != "")
            {
                ENProductora prod = new ENProductora();
                prod.Imagen = txtImg.Text;
                prod.Nombre = txtNombre.Text;
                prod.Descripcion = txtDescripcion.Text;
                prod.Web = txtWeb.Text;

                if (prod.createProductora())
                {
                    PResultado.Text = "Productora  insertada en la B.D.";
                }
                else PResultado.Text = "No es posible insertar la productora.";
            }

            else PResultado.Text = "Alguno de los campos no estan especificados.";
        }
        protected void onLeerNombre(object sender,EventArgs e)
        {
            PResultado.Text = "";
            if (txtNombre.Text == "")
                PResultado.Text = "Nombre de la productora no introducido.";
            else
            {
                ENProductora prod = new ENProductora();
                prod.Nombre = txtNombre.Text;
                DataTable dt = new DataTable();
               
                dt= prod.readProductorasNombre2();

                if (dt.Rows.Count == 0)
                {
                    PResultado.Text = "No existe una productora en la base de datos con ese nombre";
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
           
            
                ENProductora prod = new ENProductora();
                prod.Nombre = txtNombre.Text;
                DataTable dt = new DataTable();

                dt = prod.readProductoras();

                if (dt.Rows.Count == 0)
                {
                    PResultado.Text = "No hay productoras en la base de datos";
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
                PResultado.Text = "ID de la productora no introducido.";
            else
            {
                ENProductora prod = new ENProductora();
                prod.Id = int.Parse(txtId.Text);
            
                DataTable dt = new DataTable();

                 dt=prod.readProductorasId2();

                if (dt.Rows.Count == 0)
                {
                    PResultado.Text = "No existe una productora en la base de datos con ese ID "+ txtId.Text;
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
            if ( txtId.Text != "" )
            {
                ENProductora prod = new ENProductora();
               prod.Id = int.Parse(txtId.Text);
               prod.Nombre = txtNombre.Text;


                if (prod.deleteProductora())
                {

                    PResultado.Text = "Productora " + prod.Id + " borrada con éxito";
                }


                else PResultado.Text = "No es posible borrar la productora";

            }

            else PResultado.Text = "Alguno de los campos no estan especificados.";
        }
        protected void onUpdate(object sender, EventArgs e)
        {
            if (txtId.Text!="" && txtNombre.Text != "" && txtDescripcion.Text != "" && txtImg.Text != "" && txtWeb.Text != "")
            {
                ENProductora prod = new ENProductora();
                prod.Nombre = txtNombre.Text;
               prod.Descripcion = txtDescripcion.Text;
               prod.Imagen = txtImg.Text;
                prod.Web = txtWeb.Text;
                prod.Id = int.Parse(txtId.Text);

                if (prod.updateProductora())
                {
                   PResultado.Text = "Productora " + prod.Id + " actualizada con exito.";
                }
                else PResultado.Text = "Esta productora no existe en la B.D.";
            }

            else PResultado.Text = "Alguno de los campos no estan especificados.";

        }

    }

    
}