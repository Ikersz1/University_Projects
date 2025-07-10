using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENCategoria
    {
        private string descripcion_interno;
        private string nombre_interno;
        private int id_interno;

        public int id {

            get { return id_interno; }
            set { id_interno = value; }
        }

        public string nombre {
            get { return nombre_interno; }
            set { nombre_interno = value; }
        }
        public string descripcion{
            get { return descripcion_interno; }
            set { descripcion_interno = value; }
        }        

        // Constructor
        public ENCategoria()
        {

            this.id = 1;
            this.nombre = "";
            this.descripcion = "";

            
        }

        public ENCategoria(int id, string nombre, string descripcion)
        {

            this.id = id;
            this.nombre = nombre;
            this.descripcion = descripcion;
        }

        // Métodos
        public bool createCategoria()
        {
            
            bool res = true;
            CADCategoria categoria = new CADCategoria();
            res = categoria.createCategoria(this);
            return res;
        }

        public bool readCategoria()
        {
            CADCategoria categoria = new CADCategoria();
            bool res = true;
            res = categoria.readCategoria(this);
            return res;
        }

        public DataTable readCategorias()
        {
            CADCategoria categoria = new CADCategoria();
            return categoria.readCategorias();
        }

        public bool updateCategoria()
        {
            CADCategoria categoria = new CADCategoria();
            bool res = true;
            res = categoria.updateCategoria(this);
            return res;
        }

        public bool deleteCategoria()
        {
            CADCategoria categoria = new CADCategoria();
            bool res = true;
            res = categoria.deleteCategoria(this);
            return res;
        }

        public DataTable readCategoriasNombre()
        {
            CADCategoria categoria = new CADCategoria();
            return categoria.readCategoriasNombre();
        }



        /*public DataSet listas()
        {
            DataSet result = new DataSet();
            CADCategoria c = new CADCategoria();
            result = c.listas();
            return result;
        }*/
    }
}
