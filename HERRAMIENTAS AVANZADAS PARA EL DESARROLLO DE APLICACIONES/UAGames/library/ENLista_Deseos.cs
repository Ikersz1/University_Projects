using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENLista_Deseos
    {
        private int id_interno;
        private string nombre_interno, descripcion_interno;
        private ENUsuario usuario_interno;
        public int id
        {
            get { return id_interno; }
            set { id_interno = value; }
        }
        public string nombre
        {
            get { return nombre_interno; }
            set { nombre_interno = value; }
        }
        public string descripcion
        {
            get { return descripcion_interno; }
            set { descripcion_interno = value; }
        }
        public ENUsuario usuario
        {
            get { return usuario_interno; }
            set { usuario_interno = value; }
        }
        public ENLista_Deseos()
        {
            id = 0;
            nombre = "blank";
            descripcion = "blank";
            usuario = new ENUsuario();
        }
        public ENLista_Deseos(int id, string nombre, string descripcion, ENUsuario usuario)
        {
            this.id = id;
            this.nombre = nombre;
            this.descripcion = descripcion;
            this.usuario = usuario;
        }
        public bool createLista()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.createLista(this);
            return result;
        }
        public bool readLista()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.readLista(this);
            return result;
        }
        public bool readListaPorUsu()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.readListaPorUsu(this);
            return result;
        }
        public bool readFirstLista()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.readFirstLista(this);
            return result;
        }
        public bool readNextLista()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.readNextLista(this);
            return result;
        }
        public bool readPrevLista()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.readPrevLista(this);
            return result;
        }
        public bool updateLista()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.updateLista(this);
            return result;
        }
        public bool deleteLista()
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.deleteLista(this);
            return result;
        }
        public DataSet listarVjLista()
        {
            DataSet result = new DataSet();
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.listarVjLista(this);
            return result;
        }
        public bool deleteVjLista(ENVideojuego vj)
        {
            bool result = false;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.deleteVjLista(vj);
            return result;
        }
        public bool addVideojuegoLista(int videojuego)
        {
            bool result = true;
            CADLista_Deseos c = new CADLista_Deseos();
            result = c.addVideojuegoLista(this, videojuego);
            return result;
        }
    }
}
