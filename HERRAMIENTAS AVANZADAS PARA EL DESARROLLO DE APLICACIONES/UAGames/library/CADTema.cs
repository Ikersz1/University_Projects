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
    class CADTema
    {
        private string conexionBBDD;
        private SqlConnection c;

        public CADTema()
        {
            conexionBBDD = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
            c = null;
        }

        public bool createTema(ENTema tema)
        {
            string query = "Insert into Tema (titulo, foroID) values " + "('" + tema.titulo + "'," + tema.foro.id + ")";
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
        public bool readTema(ENTema tema) //selecciona un tema indicado por su id
        {
            bool sigue_while = true;
            string query = "Select * from Tema where id = " + tema.id;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == tema.id)
                    {
                        sigue_while = false;
                        tema.titulo = dr["titulo"].ToString();
                        tema.foro.id = (int)dr["foroID"];
                    }
                }
                if (sigue_while == true)
                {
                    throw new Exception("No se ha encontrado el tema con la ID indicada");
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
        public bool readFirstTema(ENTema tema) //recoge todos los temas y devuelve el primero
        {
            string query = "Select * from Tema";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                dr.Read();
                tema.id = (int)dr["id"];
                tema.titulo = dr["titulo"].ToString();
                tema.foro.id = (int)dr["foroID"];
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

        public bool readNextTema(ENTema tema) //recoge todos los temas y devuelve el siguiente
        {
            bool sigue_while = true;
            string query = "Select * from Tema";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == tema.id)
                    {
                        sigue_while = false;
                        bool siguiente = dr.Read(); //pasa al siguiente campo
                        if (siguiente == true)
                        {
                            tema.id = (int)dr["id"];
                            tema.titulo = dr["titulo"].ToString();
                            tema.foro.id = (int)dr["foroID"];
                        }
                        else
                            throw new Exception("No hay siguiente tema");
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

        public bool readPrevTema(ENTema tema) //recoge todos los temas y devuelve el anterior
        {
            bool sigue_while = true;
            string query = "Select * from Tema";

            string titulo = "blank";
            int id = 0, fid = 0;

            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == tema.id)
                    {
                        if (titulo == "blank")
                            throw new Exception("No se ha encontrado un tema anterior");
                        sigue_while = false;
                        tema.titulo = titulo;
                        tema.id = id;
                        tema.foro.id = fid;
                    }
                    else
                    {
                        id = (int)dr["id"];
                        titulo = dr["titulo"].ToString();
                        fid = (int)dr["foroID"];
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

        public bool updateTema(ENTema tema) //actualiza los datos de un tema según su id
        {
            string query_comprueba = "Select * from Tema";
            string query = "Update Tema set titulo = '" + tema.titulo + "' where id = " + tema.id;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == tema.id)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado un tema con la id indicada");
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

        public bool deleteTema(ENTema tema) //elimina el tema con la id indicada
        {
            string query_comprueba = "Select * from Tema";
            string query = "Delete from Tema where id = " + tema.id;
            bool sigue_while = true;
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand comprueba = new SqlCommand(query_comprueba, c);
                SqlDataReader dr = comprueba.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if ((int)dr["id"] == tema.id)
                    {
                        sigue_while = false;
                    }
                }
                if (sigue_while == true)
                    throw new Exception("No se ha encontrado un tema con la id indicada");
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
        public DataSet readAllTema(string foro)
        {
            DataSet data = new DataSet();
            c = new SqlConnection(conexionBBDD);
            string query = "Select titulo from Tema join Foro as f on foroID = f.id where f.nombre = '" + foro + "'";
            SqlDataAdapter da = new SqlDataAdapter(query, c);
            da.Fill(data, "Tema");
            return data;
        }
        public bool readTemaTitulo(ENTema tema) //selecciona las id asociadas con un tema por su título
        {
            bool sigue_while = true;
            string query = "Select * from Tema where titulo = '" + tema.titulo + "'";
            try
            {
                c = new SqlConnection(conexionBBDD);
                c.Open();
                SqlCommand com = new SqlCommand(query, c);
                SqlDataReader dr = com.ExecuteReader();
                while (dr.Read() && sigue_while == true)
                {
                    if (dr["titulo"].ToString() == tema.titulo)
                    {
                        sigue_while = false;
                        tema.id = int.Parse(dr["id"].ToString());
                        tema.foro.id = int.Parse(dr["foroID"].ToString());
                    }
                }
                if (sigue_while == true)
                {
                    throw new Exception("No se ha encontrado el tema con el título indicado");
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
    }
}
