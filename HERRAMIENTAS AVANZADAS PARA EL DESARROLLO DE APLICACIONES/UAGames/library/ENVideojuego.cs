using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENVideojuego
    {
        //Identificador del videojuego
        private int _Id;
        //Titulo del videojuego
        private string _Titulo;
        //Descripcion del videojuego
        private string _Descripcion;
        //Fecha de lanzamiento del videojuego
        private DateTime _FechaLanzamiento;
        //Plataforma del videojuego
        private string _Plataforma;
        //Precio del videojuego
        private double _Precio;
        //Imagen del videojuego
        private string _Imagen;
        //Productora responsable del videojuego
        private ENProductora _Productora;
        //Categoria del videojuego
        private ENCategoria _Categoria;

        //Propiedades
        public int Id
        {
            get { return _Id; }
            set { _Id = value; }
        }
        public string Titulo
        {
            get { return _Titulo; }
            set { _Titulo = value; }
        }
        public string Descripcion
        {
            get { return _Descripcion; }
            set { _Descripcion = value; }
        }
        public DateTime FechaLanzamiento
        {
            get { return _FechaLanzamiento; }
            set { _FechaLanzamiento = value; }
        }
        public string Plataforma
        {
            get { return _Plataforma; }
            set { _Plataforma = value; }
        }
        public string Imagen
        {
            get { return _Imagen; }
            set { _Imagen = value; }
        }
        public double Precio
        {
            get { return _Precio; }
            set { _Precio = value; }
        }
        public ENProductora Productora
        {
            get { return _Productora; }
            set { _Productora = value; }
        }
        public ENCategoria Categoria
        {
            get { return _Categoria; }
            set { _Categoria = value; }
        }

        //Constructor por defecto
        public ENVideojuego()
        {
                Id = 0;
                Titulo = "";
                Descripcion = "";
                FechaLanzamiento = DateTime.UtcNow;
                Plataforma = "";
                Imagen = "";
                Precio = 0.0;
                Productora = new ENProductora();
                Categoria = new ENCategoria();
        }

        //Constructor con parametros
        public ENVideojuego(int id, String titulo, String descripcion, DateTime fecha, String plataforma, String imagen, double precio, ENProductora productora, ENCategoria categoria)
        {
            Id = id;
            Titulo = titulo;
            Descripcion = descripcion;
            FechaLanzamiento = fecha;
            Plataforma = plataforma;
            Imagen = imagen;
            Precio = precio;
            Productora = productora;
            Categoria = categoria;
        }

        public bool addVideojuego()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.addVideojuego(this);

        }

        public bool readVideojuego()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.readVideojuego(this);
        }

        public bool readVideojuegoId()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.readVideojuegoId(this);
        }

        public DataTable readVideojuegos()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.readVideojuegos();
        }

        public DataTable readVideojuegos(string query)
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.readVideojuegos(query);
        }

        public DataTable readVideojuegosProductora()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.readVideojuegosProductora(this);
        }

        public DataTable readVideojuegosCategoria()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.readVideojuegosCategoria(this);
        }

        public bool updateVideojuego()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.updateVideojuego(this);
        }

        public DataTable updateVideojuego(int i)
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.updateVideojuego(this, i);
        }

        public bool deleteVideojuego()
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.deleteVideojuego(this);
            
        }

        public DataTable deleteVideojuego(int i)
        {
            CADVideojuego cad = new CADVideojuego();
            return cad.deleteVideojuego(this, i);
        }

    }
}
