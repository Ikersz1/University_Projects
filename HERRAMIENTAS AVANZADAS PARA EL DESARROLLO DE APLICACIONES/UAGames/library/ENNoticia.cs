using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SqlClient;
using System.Data;

namespace library
{
    public class ENNoticia
    {
        private int id;
        private string titulo;
        private DateTime fecha_public;
        private string contenido;
        private int productoraID;
        private string imagen;

        public string Imagen
        {
            get { return imagen; }
            set { imagen= value; }
        }
        public int Id
        {
            get { return id; }
            set { id = value; }
        }

        public string Titulo
        {
            get { return titulo; }
            set { titulo = value; }
        }

        public DateTime FechaPublicacion
        {
            get { return fecha_public; }
            set { fecha_public = value; }
        }

        public string Contenido
        {
            get { return contenido; }
            set { contenido = value; }
        }
        public int ProductoraID
        {
            get { return productoraID; }
            set { productoraID = value; }
        }
        public ENNoticia()
        {
            id = 0;
            titulo = "";
            fecha_public = DateTime.UtcNow;
            contenido = "";
            productoraID = 0;
            Imagen = "";

        }

        public ENNoticia(int id, string titulo, DateTime fecha_public, string contenido, int productora,string img)
        {
            this.Imagen = img;
            this.id = id;
            this.titulo = titulo;
            this.fecha_public = DateTime.UtcNow;
            this.contenido = contenido;
            this.productoraID = productora;
        }
        public bool createNoticia()
        {

            CADNoticia nuevaNoticia = new CADNoticia();
            return  nuevaNoticia.createNoticia(this);
           
        }
        
        public bool updateNoticia()
        {
            CADNoticia prod = new CADNoticia();
            bool updated = prod.updateNoticia(this);
            return updated;
        }
        public bool deleteNoticia()
        {
            CADNoticia prod = new CADNoticia();
            bool deleted = prod.deleteNoticia(this);
            return deleted;
        }
        public DataSet readNoticia()
        {
            DataSet result = new DataSet();
            CADNoticia c = new CADNoticia();
            result = c.readNoticia();
            return result;
        }
        public DataTable readNoticias()
        {
            CADNoticia noticia = new CADNoticia();
            return noticia.readNoticiass();
        }
        public DataTable readNoticiasTitulo2()
        {
            CADNoticia noticia = new CADNoticia();
            return noticia.readNoticiaTitulo2(this);
        }
        public DataTable readNoticiasId2()
        {
            CADNoticia noticia = new CADNoticia();
            return noticia.readNoticiasId2(this);
        }

    }
}


    


