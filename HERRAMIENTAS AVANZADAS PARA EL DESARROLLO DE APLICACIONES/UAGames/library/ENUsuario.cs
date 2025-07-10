using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    public class ENUsuario
    {
        private int id_interno;
        private bool admin_interno;
        private string nick_interno, nombre_interno, apell_interno, email_interno, passw_interno, telef_interno;
        private DateTime fecha_interno;

        public int id
        {
            get { return id_interno; }
            set { id_interno = value; }
        }

        public string nick
        {
            get { return nick_interno; }
            set { nick_interno = value; }
        }
        public string nombre
        {
            get { return nombre_interno; }
            set { nombre_interno = value; }
        }
        public string apellidos
        {
            get { return apell_interno; }
            set { apell_interno = value; }
        }
        public string email
        {
            get { return email_interno; }
            set { email_interno = value; }
        }
        public string password
        {
            get { return passw_interno; }
            set { passw_interno = value; }
        }
        public DateTime fecha_nac
        {
            get { return fecha_interno; }
            set { fecha_interno = value; }
        }
        public string telef
        {
            get { return telef_interno; }
            set { telef_interno = value; }
        }
        public bool admin
        {
            get { return admin_interno; }
            set { admin_interno = value; }
        }
        public ENUsuario()
        {
            id = 0;
            nick = "blank";
            nombre = "blank";
            apellidos = "blank";
            email = "blank";
            password = "blank";
            fecha_nac = DateTime.Now;
            telef = "blank";
            admin = false;
        }
        public ENUsuario(int id,string nick, string nombre, string apellidos, string email, string password, DateTime fecha_nac, string telef, bool admin)
        {
            this.id = id;
            this.nick = nick;
            this.nombre = nombre;
            this.apellidos = apellidos;
            this.email = email;
            this.password = password;
            this.fecha_nac = fecha_nac;
            this.telef = telef;
            this.admin = admin;
        }

        public bool createUsuario()
        {
            bool result = true;
            CADUsuario c = new CADUsuario();
            result = c.createUsuario(this);
            return result;
        }

        public bool readUsuario()
        {
            bool result = true;
            CADUsuario c = new CADUsuario();
            result = c.readUsuario(this);
            return result;
        }

        public bool readFirstUsuario()
        {
            bool result = true;
            CADUsuario c = new CADUsuario();
            result = c.readFirstUsuario(this);
            return result;
        }

        public bool readNextUsuario()
        {
            bool result = true;
            CADUsuario c = new CADUsuario();
            result = c.readNextUsuario(this);
            return result;
        }

        public bool readPrevUsuario()
        {
            bool result = true;
            CADUsuario c = new CADUsuario();
            result = c.readPrevUsuario(this);
            return result;
        }

        public bool updateUsuario()
        {
            bool result = true;
            CADUsuario c = new CADUsuario();
            result = c.updateUsuario(this);
            return result;
        }

        public bool deleteUsuario()
        {
            bool result = true;
            CADUsuario c = new CADUsuario();
            result = c.deleteUsuario(this);
            return result;
        }
        public DataSet listarClientesD()
        {
            DataSet result = new DataSet();
            CADUsuario c = new CADUsuario();
            result = c.listarClientesD();
            return result;
        }
    }
}
