using System;
using System.Collections.Generic;
using System.Data;
using System.Globalization;
using System.Linq;
using System.Text;
using System.Data;
using System.Threading.Tasks;

namespace library
{
    //Codigo Omar

    public class ENReview
    {
        public ENUsuario usuario { get; set; }
        public ENVideojuego videojuego { get; set; }
        public int id { get; set; }
        public DateTime fecha { get; set; }
        public string comentario { get; set; }
        public int puntuacion { get; set; }
        private CADReview review { get; set; }

        public ENReview()
        {
            this.usuario = new ENUsuario();
            this.videojuego = new ENVideojuego();
            this.fecha = DateTime.Now;
            this.puntuacion = 0;
            this.comentario = "";
            this.review = new CADReview();
        }

        public ENReview(DateTime fecha, int puntuacion, string comentario, ENVideojuego videojuego, ENUsuario usuario)
        {
            this.usuario = usuario;
            this.videojuego = videojuego;
            this.fecha = fecha;
            this.puntuacion = puntuacion;
            this.comentario = comentario;
            this.review = new CADReview();
        }

        public bool createReview()
        {
            return this.review.createReview(this);
        }
        public bool deleteReview()
        {
            return this.review.deleteReview(this);
        }
        public bool updateReview()
        {
            return this.review.updateReview(this);
        }
        public bool readReview()
        {
            return this.review.readReview(this);
        }

        public DataTable misReviews()
        {
            return this.review.misReviews(this);
        }

        public DataTable filtrarReview(string ordenColumna)
        {
            return this.review.filtrarReview(this, ordenColumna);
        }

        public DataTable mostrarReview()
        {
            return this.review.mostrarReview(this);
        }
    }

}
