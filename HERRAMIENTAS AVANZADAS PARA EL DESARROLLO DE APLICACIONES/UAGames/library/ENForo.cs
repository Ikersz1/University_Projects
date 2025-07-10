using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENForo
    {
        private string nombre_interno;
        private int id_interno;
        public string nombre
        {
            get { return nombre_interno; }
            set { nombre_interno = value; }
        }
        public int id
        {
            get { return id_interno; }
            set { id_interno = value; }
        }
        public ENForo()
        {
            id = 0;
            nombre = "blank";
        }
        public ENForo(int id, string nombre)
        {
            this.id = id;
            this.nombre = nombre;
        }
        public bool createForo()
        {
            bool result = true;
            CADForo c = new CADForo();
            result = c.createForo(this);
            return result;
        }
        public bool readForo()
        {
            bool result = true;
            CADForo c = new CADForo();
            result = c.readForo(this);
            return result;
        }
        public bool readFirstForo()
        {
            bool result = true;
            CADForo c = new CADForo();
            result = c.readFirstForo(this);
            return result;
        }
        public bool readNextForo()
        {
            bool result = true;
            CADForo c = new CADForo();
            result = c.readNextForo(this);
            return result;
        }
        public bool readPrevForo()
        {
            bool result = true;
            CADForo c = new CADForo();
            result = c.readPrevForo(this);
            return result;
        }
        public bool updateForo()
        {
            bool result = true;
            CADForo c = new CADForo();
            result = c.updateForo(this);
            return result;
        }
        public bool deleteForo()
        {
            bool result = true;
            CADForo c = new CADForo();
            result = c.deleteForo(this);
            return result;
        }
        public DataSet readAllForo()
        {
            DataSet data = new DataSet();
            CADForo c = new CADForo();
            data = c.readAllForo();
            return data;
        }
    }
}
