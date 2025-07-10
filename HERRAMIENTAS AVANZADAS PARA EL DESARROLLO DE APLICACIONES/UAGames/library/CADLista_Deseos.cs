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
    class CADLista_Deseos
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADLista_Deseos()
        {
            conexionBBDD = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
            c = null;
        }

        public bool createLista(ENLista_Deseos lista)
        {
            string query = "Insert into ListaDeseos (nombre, descripcion, usuarioID) values " + "('" + lista.nombre + "','" + lista.descripcion + "'," + lista.usuario.id + ")";
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
        public bool readLista(ENLista_Deseos lista) //selecciona la lista de un usuario indicado por su id
        {
            bool sigue_while = true;
            string query = "Select * from ListaDeseos where id = " + lista.id;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == lista.id)
                    {
                        sigue_while = false;
                        lista.usuario.id = (int)dr["usuarioID"];
                        lista.nombre = dr["nombre"].ToString();
                        lista.descripcion = dr["descripcion"].ToString();
                    }
                }
                if (sigue_while == true)
                {
                    throw new Exception("No se ha encontrado la lista con la ID indicada");
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
        public bool readListaPorUsu(ENLista_Deseos lista) //selecciona la lista de un usuario indicado por la id del usuario
        {
            bool sigue_while = true;
            string query = "Select * from ListaDeseos where usuarioID = " + lista.usuario.id;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["usuarioID"] == lista.usuario.id)
                    {
                        sigue_while = false;
                        lista.id = (int)dr["id"];
                        lista.nombre = dr["nombre"].ToString();
                        lista.descripcion = dr["descripcion"].ToString();
                    }
                }
                if (sigue_while == true)
                {
                    throw new Exception("No se ha encontrado la lista con la ID de usuario indicada");
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
        public bool readFirstLista(ENLista_Deseos lista) //recoge todas las listas y devuelve la primera
        {
            string query = "Select * from ListaDeseos";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                dr.Read();
                lista.id = (int)dr["id"];
                lista.nombre = dr["nombre"].ToString();
                lista.descripcion = dr["descripcion"].ToString();
                lista.usuario.id = (int)dr["usuarioID"];
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

        public bool readNextLista(ENLista_Deseos lista) //recoge todas las listas y devuelve la siguiente
        {
            bool sigue_while = true;
            string query = "Select * from ListaDeseos";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == lista.id)
                    {
                        sigue_while = false;
                        bool siguiente = dr.Read(); //pasa al siguiente campo
                        if (siguiente == true)
                        {
                            lista.id = (int)dr["id"];
                            lista.nombre = dr["nombre"].ToString();
                            lista.descripcion = dr["descripcion"].ToString();
                            lista.usuario.id = (int)dr["usuarioID"];
                        }
                        else
                            throw new Exception("No hay siguiente lista");
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

        public bool readPrevLista(ENLista_Deseos lista) //recoge todas las listas y devuelve la anterior
        {
            bool sigue_while = true;
            string query = "Select * from ListaDeseos";

            string nom = "blank";
            string desc = "blank";
            int id = 0, uid = 0;

            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == lista.id)
                    {
                        if (nom == "blank")
                            throw new Exception("No se ha encontrado una lista anterior");
                        sigue_while = false;
                        lista.id = id;
                        lista.nombre = nom;
                        lista.descripcion = desc;
                        lista.usuario.id = uid;
                    }
                    else
                    {
                        id = (int)dr["id"];
                        nom = dr["nombre"].ToString();
                        desc = dr["descripcion"].ToString();
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

        public bool updateLista(ENLista_Deseos lista) //actualiza los datos de una lista según su id de usuario
        {
            string query_comprueba = "Select * from ListaDeseos";
            string query = "Update ListaDeseos set nombre = '" + lista.nombre + "', descripcion = '" + lista.descripcion + "' where id = " + lista.id;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == lista.id)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado una lista con la id indicada");
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

        public bool deleteLista(ENLista_Deseos lista) //elimina la lista con la id indicada
        {
            string query_comprueba = "Select * from ListaDeseos";
            string query = "Delete from ListaDeseos where id = " + lista.id;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == lista.id)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado una lista con la id indicada");
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
        public DataSet listarVjLista(ENLista_Deseos lista)
        {
            DataSet bdvirtual = new DataSet();

            string query = "select v.titulo, v.fecha_lanzamiento from Videojuego as v join ListaDeseosVideojuego as dv on v.id=dv.videojuegoID join ListaDeseos as d on dv.listaDeseosID=d.id where d.usuarioID = " + lista.usuario.id;
            SqlConnection c = new SqlConnection(conexionBBDD);
            SqlDataAdapter da = new SqlDataAdapter(query, c);
            da.Fill(bdvirtual, "Videojuego");
            return bdvirtual;
        }
        public bool deleteVjLista(ENVideojuego vj)
        {
            string query_comprueba = "Select * from ListaDeseosVideojuego";
            string query = "Delete from ListaDeseosVideojuego where videojuegoID = " + vj.Id;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["videojuegoID"] == vj.Id)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado un videojuego con la id indicada");
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
        public bool addVideojuegoLista(ENLista_Deseos lista, int videojuego)
        {
            string query = "Insert into ListaDeseosVideojuego (listaDeseosID, videojuegoID) values " + "(" + lista.id + "," + videojuego + ")";
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
    }
}
