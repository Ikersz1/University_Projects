using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENTema
    {
        private int id_interno;
        private string titulo_interno;
        private ENForo foro_interno;
        public int id
        {
            get { return id_interno; }
            set { id_interno = value; }
        }
        public string titulo
        {
            get { return titulo_interno; }
            set { titulo_interno = value; }
        }
        public ENForo foro
        {
            get { return foro_interno; }
            set { foro_interno = value; }
        }
        public ENTema()
        {
            id = 0;
            titulo = "blank";
            foro = new ENForo();
        }
        public ENTema(int id, string titulo, ENForo foro)
        {
            this.id = id;
            this.titulo = titulo;
            this.foro = foro;
        }
        public bool createTema()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.createTema(this);
            return result;
        }
        public bool readTema()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.readTema(this);
            return result;
        }
        public bool readFirstTema()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.readFirstTema(this);
            return result;
        }
        public bool readNextTema()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.readNextTema(this);
            return result;
        }
        public bool readPrevTema()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.readPrevTema(this);
            return result;
        }
        public bool updateTema()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.updateTema(this);
            return result;
        }
        public bool deleteTema()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.deleteTema(this);
            return result;
        }
        public DataSet readAllTema(string foro)
        {
            DataSet data = new DataSet();
            CADTema c = new CADTema();
            data = c.readAllTema(foro);
            return data;
        }
        public bool readTemaTitulo()
        {
            bool result = true;
            CADTema c = new CADTema();
            result = c.readTemaTitulo(this);
            return result;
        }
    }
}
