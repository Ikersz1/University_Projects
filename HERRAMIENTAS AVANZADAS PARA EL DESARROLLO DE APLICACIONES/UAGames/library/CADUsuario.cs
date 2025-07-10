using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Configuration;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Globalization;
using System.Data;

namespace library
{
    public class CADUsuario
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADUsuario()
        {
            conexionBBDD = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
            c = null;
        }

        public bool createUsuario(ENUsuario usuario)
        {
            string fechaFormatoCorrecto = usuario.fecha_nac.ToString("yyyy-MM-dd");
            string query = "Insert into Usuario (nick, nombre, apellidos, email, password, fecha_nacimiento, telefono, admin) values " +
                "('" + usuario.nick + "','" + usuario.nombre + "','" + usuario.apellidos + "','" + usuario.email + "','" + usuario.password 
                + "','" + fechaFormatoCorrecto + "','" + usuario.telef + "'," + Convert.ToInt32(usuario.admin) + ")";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                com.ExecuteNonQuery();
                c.Close();
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }
            finally
            {
                if (c != null)
                    c.Close();
            }
            return true;
        }
        public bool readUsuario(ENUsuario usuario) //busca un usuario por su nick, que es único
        {
            bool sigue_while = true;
            string query = "Select * from [Usuario] where nick = '" + usuario.nick + "';";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if (dr["nick"].ToString() == usuario.nick)
                    {
                        sigue_while = false;
                        usuario.id = (int)dr["id"];
                        usuario.nombre = dr["nombre"].ToString();
                        usuario.apellidos = dr["apellidos"].ToString();
                        usuario.email = dr["email"].ToString();
                        usuario.password = dr["password"].ToString();
                        usuario.fecha_nac = (DateTime)dr["fecha_nacimiento"];
                        usuario.telef = dr["telefono"].ToString();
                        usuario.admin = Convert.ToBoolean(dr["admin"]);
                    }
                }
                if (sigue_while == true)
                {
                    throw new Exception("No se ha encontrado el usuario");
                }
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }
            finally
            {
                if (c != null)
                    c.Close();
            }
            return true;
        }
        public bool readFirstUsuario(ENUsuario usuario) //recoge todos los usuarios y devuelve el primero
        {
            string query = "Select * from Usuario";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                dr.Read();
                usuario.id = (int)dr["id"];
                usuario.nick = dr["nick"].ToString();
                usuario.nombre = dr["nombre"].ToString();
                usuario.apellidos = dr["apellidos"].ToString();
                usuario.email = dr["email"].ToString();
                usuario.password = dr["password"].ToString();
                usuario.fecha_nac = (DateTime)dr["fecha_nacimiento"];
                usuario.telef = dr["telefono"].ToString();
                usuario.admin = Convert.ToBoolean(dr["admin"]);
                dr.Close();
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }
            finally
            {
                if (c != null)
                    c.Close();
            }
            return true;
        }

        public bool readNextUsuario(ENUsuario usuario) //recoge todos los usuarios y devuelve el siguiente
        {
            bool sigue_while = true;
            string query = "Select * from Usuario";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if (dr["nick"].ToString() == usuario.nick)
                    {
                        sigue_while = false;
                        bool siguiente = dr.Read(); //pasa al siguiente campo
                        if (siguiente == true)
                        {
                            usuario.id = (int)dr["id"];
                            usuario.nick = dr["nick"].ToString();
                            usuario.nombre = dr["nombre"].ToString();
                            usuario.apellidos = dr["apellidos"].ToString();
                            usuario.email = dr["email"].ToString();
                            usuario.password = dr["password"].ToString();
                            usuario.fecha_nac = (DateTime)dr["fecha_nacimiento"];
                            usuario.telef = dr["telefono"].ToString();
                            usuario.admin = Convert.ToBoolean(dr["admin"]);
                        }
                        else
                            throw new Exception("No hay siguiente usuario");
                    }
                }
                dr.Close();
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }
            finally
            {
                if (c != null)
                    c.Close();
            }
            return true;
        }

        public bool readPrevUsuario(ENUsuario usuario) //recoge todos los usuarios y devuelve el anterior
        {
            bool sigue_while = true;
            string query = "Select * from Usuario";

            int id = 0;
            string nick = "blank";
            string nom = "blank";
            string apellidos = "blank";
            string email = "blank";
            string password = "blank";
            DateTime fecha_nac = DateTime.Now;
            string telef = "blank";
            bool admin = false;

            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if (dr["nick"].ToString() == usuario.nick)
                    {
                        if (nick == "blank")
                            throw new Exception("No se ha encontrado un usuario anterior");
                        sigue_while = false;
                        usuario.id = id;
                        usuario.nick = nick;
                        usuario.nombre = nom;
                        usuario.apellidos = apellidos;
                        usuario.email = email;
                        usuario.password = password;
                        usuario.fecha_nac =fecha_nac;
                        usuario.telef = telef;
                        usuario.admin = admin;
                    }
                    else
                    {
                        id = (int)dr["id"];
                        nick = dr["nick"].ToString();
                        nom = dr["nombre"].ToString();
                        apellidos = dr["apellidos"].ToString();
                        email = dr["email"].ToString();
                        password = dr["password"].ToString();
                        fecha_nac = (DateTime)dr["fecha_nacimiento"];
                        telef = dr["telefono"].ToString();
                        admin = Convert.ToBoolean(dr["admin"]);
                    }
                }
                dr.Close();
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }
            finally
            {
                if (c != null)
                    c.Close();
            }
            return true;
        }

        public bool updateUsuario(ENUsuario usuario) //actualiza los datos de un usuario según su nick
        {
            string fechaFormatoCorrecto = usuario.fecha_nac.ToString("yyyy-MM-dd");
            string query_comprueba = "Select * from Usuario";
            string query = "Update Usuario set nombre = '" + usuario.nombre + "', apellidos = '" + usuario.apellidos + "', email = '" + usuario.email + "', password = '" + usuario.password
                 + "', fecha_nacimiento = '" + fechaFormatoCorrecto + "', telefono = '" + usuario.telef + "', admin = '" + Convert.ToInt32(usuario.admin) + "' where nick = '" + usuario.nick + "'";
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if (dr["nick"].ToString() == usuario.nick)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado un usuario con el mismo nick");
                dr.Close();
                SqlCommand com = new SqlCommand(query, c);
                com.ExecuteNonQuery();
                dr.Close();
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }
            finally
            {
                if (c != null)
                    c.Close();
            }
            return true;
        }

        public bool deleteUsuario(ENUsuario usuario) //elimina el usuario con el nick indicado
        {
            string query_comprueba = "Select * from Usuario";
            string query = "Delete from Usuario where nick = " + usuario.nick;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if (dr["nick"].ToString() == usuario.nick)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado un usuario con el mismo nick");
                dr.Close();
                SqlCommand com = new SqlCommand(query, c);
                com.ExecuteNonQuery();
                dr.Close();
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                return false;
            }
            finally
            {
                if (c != null)
                    c.Close();
            }
            return true;
        }
        public DataSet listarClientesD()
        {
            DataSet bdvirtual = new DataSet();

            SqlConnection c = new SqlConnection(conexionBBDD);
            SqlDataAdapter da = new SqlDataAdapter("select * from Usuario", c);
            da.Fill(bdvirtual, "Usuario");
            return bdvirtual;
        }
    }
}
