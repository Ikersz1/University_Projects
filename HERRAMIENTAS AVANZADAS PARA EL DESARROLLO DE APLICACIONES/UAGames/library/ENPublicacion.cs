using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENPublicacion
    {
        private int id_interno;
        private string text_interno;
        private ENTema tema_interno;
        private ENUsuario usuario_interno;
        public int id
        {
            get { return id_interno; }
            set { id_interno = value; }
        }
        public string text
        {
            get { return text_interno; }
            set { text_interno = value; }
        }
        public ENTema tema
        {
            get { return tema_interno; }
            set { tema_interno = value; }
        }
        public ENUsuario usuario
        {
            get { return usuario_interno; }
            set { usuario_interno = value; }
        }
        public ENPublicacion()
        {
            id = 0;
            text = "blank";
            tema = new ENTema();
            usuario = new ENUsuario();
        }
        public ENPublicacion(int id, string text, ENTema tema, ENUsuario usuario)
        {
            this.id = id;
            this.text = text;
            this.tema = tema;
            this.usuario = usuario;
        }
        public bool createPublicacion()
        {
            bool result = true;
            CADPublicacion c = new CADPublicacion();
            result = c.createPublicacion(this);
            return result;
        }
        public bool readPublicacion()
        {
            bool result = true;
            CADPublicacion c = new CADPublicacion();
            result = c.readPublicacion(this);
            return result;
        }
        public bool readFirstPublicacion()
        {
            bool result = true;
            CADPublicacion c = new CADPublicacion();
            result = c.readFirstPublicacion(this);
            return result;
        }
        public bool readNextPublicacion()
        {
            bool result = true;
            CADPublicacion c = new CADPublicacion();
            result = c.readNextPublicacion(this);
            return result;
        }
        public bool readPrevPublicacion()
        {
            bool result = true;
            CADPublicacion c = new CADPublicacion();
            result = c.readPrevPublicacion(this);
            return result;
        }
        public bool updatePublicacion()
        {
            bool result = true;
            CADPublicacion c = new CADPublicacion();
            result = c.updatePublicacion(this);
            return result;
        }
        public bool deletePublicacion()
        {
            bool result = true;
            CADPublicacion c = new CADPublicacion();
            result = c.deletePublicacion(this);
            return result;
        }
        public DataSet listarClientesD()
        {
            DataSet result = new DataSet();
            CADPublicacion c = new CADPublicacion();
            result = c.listarClientesD();
            return result;
        }
        public DataSet listarClientesDPublico(string tema)
        {
            DataSet result = new DataSet();
            CADPublicacion c = new CADPublicacion();
            result = c.listarClientesDPublico(tema);
            return result;
        }
    }
}
