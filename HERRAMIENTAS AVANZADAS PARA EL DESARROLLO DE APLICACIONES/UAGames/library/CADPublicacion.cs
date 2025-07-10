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
    class CADPublicacion
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADPublicacion()
        {
            conexionBBDD = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
            c = null;
        }

        public bool createPublicacion(ENPublicacion publicacion)
        {
            string query = "Insert into Publicacion (text, temaID, usuarioID) values "
                + "('" + publicacion.text + "','" + publicacion.tema.id + "','" + publicacion.usuario.id + "')";
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
        public bool readPublicacion(ENPublicacion publicacion) //selecciona una publicacion por su id
        {
            bool sigue_while = true;
            string query = "Select * from Publicacion where id = " + publicacion.id;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == publicacion.id)
                    {
                        sigue_while = false;
                        publicacion.text = dr["text"].ToString();
                        publicacion.tema.id = (int)dr["temaID"];
                        publicacion.usuario.id = (int)dr["usuarioID"];
                    }
                }
                if (sigue_while == true)
                {
                    throw new Exception("No se ha encontrado la publicación con la ID indicada");
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
        public bool readFirstPublicacion(ENPublicacion publicacion) //recoge todas las publicaciones y devuelve la primera
        {
            string query = "Select * from Publicacion";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                dr.Read();
                publicacion.id = (int)dr["id"];
                publicacion.text = dr["text"].ToString();
                publicacion.tema.id = (int)dr["temaID"];
                publicacion.usuario.id = (int)dr["usuarioID"];
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

        public bool readNextPublicacion(ENPublicacion publicacion) //recoge todas las publicaciones y devuelve la siguiente
        {
            bool sigue_while = true;
            string query = "Select * from Publicacion";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == publicacion.id)
                    {
                        sigue_while = false;
                        bool siguiente = dr.Read(); //pasa al siguiente campo
                        if (siguiente == true)
                        {
                            publicacion.id = (int)dr["id"];
                            publicacion.text = dr["text"].ToString();
                            publicacion.tema.id = (int)dr["temaID"];
                            publicacion.usuario.id = (int)dr["usuarioID"];
                        }
                        else
                            throw new Exception("No hay siguiente publicación");
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

        public bool readPrevPublicacion(ENPublicacion publicacion) //recoge todas las publicaciones y devuelve la anterior
        {
            bool sigue_while = true;
            string query = "Select * from Publicacion";

            string text = "blank";
            int id = 0, tid = 0, uid = 0;

            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == publicacion.id)
                    {
                        if (text == "blank")
                            throw new Exception("No se ha encontrado una publicación anterior");
                        sigue_while = false;
                        publicacion.id = id;
                        publicacion.text = text;
                        publicacion.tema.id = tid;
                        publicacion.usuario.id = uid;
                    }
                    else
                    {
                        id = (int)dr["id"];
                        text = dr["text"].ToString();
                        tid = (int)dr["temaID"];
                        uid = (int)dr["usuarioID"];
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

        public bool updatePublicacion(ENPublicacion publicacion) //actualiza los datos de una publicación según su id
        {
            string query_comprueba = "Select * from Publicacion";
            string query = "Update Publicacion set text = '" + publicacion.text + "' where id = " + publicacion.id;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == publicacion.id)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado una publicación con la id indicada");
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

        public bool deletePublicacion(ENPublicacion publicacion) //elimina la publicación con la id indicada
        {
            string query_comprueba = "Select * from Publicacion";
            string query = "Delete from Publicacion where id = " + publicacion.id;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == publicacion.id)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado una publicación con la id indicada");
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
            string query = "select p.id as ID_Publicación, p.text as Texto, p.usuarioID as ID_Usuario, t.id as ID_Tema, t.titulo as Título_Tema, f.id as ID_Foro, f.nombre as Nombre_Foro from Publicacion as p join Tema as t on temaID = t.id join Foro as f on foroID = f.id";
            SqlDataAdapter da = new SqlDataAdapter(query, c);
            da.Fill(bdvirtual, "Publicacion");
            return bdvirtual;
        }
        public DataSet listarClientesDPublico(string tema)
        {
            DataSet bdvirtual = new DataSet();

            SqlConnection c = new SqlConnection(conexionBBDD);
            string query = "select p.text as Texto, u.nick as Usuario from Usuario u join Publicacion as p on usuarioID = u.id join Tema as t on temaID = t.id where Titulo = '" + tema + "'";
            SqlDataAdapter da = new SqlDataAdapter(query, c);
            da.Fill(bdvirtual, "Publicacion");
            return bdvirtual;
        }
    }
}
