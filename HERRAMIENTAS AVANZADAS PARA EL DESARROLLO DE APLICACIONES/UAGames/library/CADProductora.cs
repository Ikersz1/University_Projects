using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SqlTypes;
using System.Data.SqlClient;
using System.Data.Common;
using System.Data;
using System.Configuration;

namespace library
{
    class CADProductora
    {
        private String datos;
        private SqlConnection con;
        public CADProductora()
        {
            datos = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
            con = null;
        }
        public bool readProductora(ENProductora en)
        {
            bool leida = false;
            try
            {
                con = new SqlConnection(datos);
                con.Open();
                string query = "Select * From Productora Where Id='" + en.Id + "' ";
                SqlCommand consulta = new SqlCommand(query, con);
                SqlDataReader search = consulta.ExecuteReader();


                if (search.Read())
                {
                    en.Id = Int32.Parse(search["Id"].ToString());
                    en.Nombre = search["Nombre"].ToString();
                    en.Descripcion = search["Descripcion"].ToString();
                    en.Imagen = search["Imagen"].ToString();
                    en.Web = search["Web"].ToString();
                    leida = true;
                }
                search.Close();
            }
            catch (Exception ex)
            {
                throw new Exception("Error al buscar la productora en la base de datos: " + ex.Message);
            }
            finally
            {
                if (con != null)
                    con.Close();
            }

            return leida;
        }
        public bool readProductoraNombre(ENProductora en)
        {
            bool leida = false;
            try
            {
                con = new SqlConnection(datos);
                con.Open();
                string query = "Select * From Productora Where nombre='" + en.Nombre + "' ";
                SqlCommand consulta = new SqlCommand(query, con);
                SqlDataReader search = consulta.ExecuteReader();


                if (search.Read())
                {
                    en.Id = Int32.Parse(search["Id"].ToString());
                    en.Descripcion = search["Descripcion"].ToString();
                    en.Imagen = search["Imagen"].ToString();
                    en.Web = search["Web"].ToString();
                    leida = true;
                }
                search.Close();
            }
            catch (Exception ex)
            {
                throw new Exception("Error al buscar la productora en la base de datos: " + ex.Message);
            }
            finally
            {
                if (con != null)
                    con.Close();
            }

            return leida;
        }



        //Funciona
        public bool createProductora(ENProductora en)
        {
            bool creada = false;
            SqlConnection conect = null;
            string query = "INSERT INTO [Productora]" + "(nombre,descripcion,imagen,web)" + "VALUES (@nombre, @descripcion,@imagen,@web);";
            try
            {
                conect = new SqlConnection(datos);
                conect.Open();
               
                SqlCommand consulta = new SqlCommand(query, conect);
                consulta.Parameters.AddWithValue("@nombre", en.Nombre);
                consulta.Parameters.AddWithValue("@descripcion", en.Descripcion);
                consulta.Parameters.AddWithValue("@imagen", en.Imagen);
                consulta.Parameters.AddWithValue("@web", en.Web);
                consulta.ExecuteNonQuery();
                creada = true;
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("User operation has failed. Error: {0}", sqlex.Message);
                return creada;
            }
            catch (Exception ex)
            {
                creada = false;
                Console.WriteLine("User operation has failed. Error: {0}", ex.Message);
                
            }
            finally
            {
                if (conect != null)
                    conect.Close();
            }
           
            return creada;
        }

        
        


       


       

        //FUnciona

        public bool updateProductora(ENProductora en)
        {
            bool updated = false;
            try
            {
                con = new SqlConnection(datos);
                con.Open();
                string query = "UPDATE Productora SET Nombre=@Nombre, Descripcion=@Descripcion, Imagen=@Imagen, Web=@Web WHERE Id=@Id";
                SqlCommand consulta = new SqlCommand(query, con);
                consulta.Parameters.AddWithValue("@Nombre", en.Nombre);
                consulta.Parameters.AddWithValue("@Descripcion", en.Descripcion);
                consulta.Parameters.AddWithValue("@Imagen", en.Imagen);
                consulta.Parameters.AddWithValue("@Web", en.Web);
                consulta.Parameters.AddWithValue("@Id", en.Id);
                consulta.ExecuteNonQuery();
                updated = true;
            }
            catch (Exception ex)
            {
                throw new Exception("Error al actualizar la productora en la base de datos: " + ex.Message);
            }
            finally
            {
                if (con != null)
                    con.Close();

            }
            return updated;




        }
        //Funciona
        public bool deleteProductora(ENProductora en)
        {
            bool del = false;
            try
            {
                con = new SqlConnection(datos);
                con.Open();
                string query = "DELETE FROM Productora WHERE Id=@Id";
                SqlCommand consulta = new SqlCommand(query, con);
                consulta.Parameters.AddWithValue("@Id", en.Id);
                consulta.ExecuteNonQuery();
                del = true;
            }
            catch (Exception ex)
            {
                throw new Exception("Error al eliminar la productora de la base de datos: " + ex.Message);
            }
            finally
            {
                if (con != null)
                    con.Close();
            }
            return del;
        }
        //FUnciona
        public DataTable readProductorasNombre2(ENProductora en)
        {
            SqlConnection connection = null;
            DataTable productoras = new DataTable();

            try
            {
                connection = new SqlConnection(datos);
                connection.Open();

                string sentence = "SELECT nombre, id,imagen,web,descripcion FROM [Productora] where nombre='" +en.Nombre + "';";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(productoras);
                

            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return productoras;
        }
        public DataTable readProductorasId2(ENProductora en)
        {
            SqlConnection connection = null;
            DataTable productoras = new DataTable();

            try
            {
                connection = new SqlConnection(datos);
                connection.Open();

                string sentence = "SELECT nombre, id,imagen,web,descripcion FROM [Productora] where id='" + en.Id + "';";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(productoras);


            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return productoras;
        }


        public DataTable readProductorasNombre()
        {
            SqlConnection connection = null;
            DataTable productoras = new DataTable();

            try
            {
                connection = new SqlConnection(datos);
                connection.Open();

                string sentence = "SELECT nombre, id,imagen,web,descripcion FROM [Productora];";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(productoras);

            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return productoras;
        }
        //Lee y devuelve todas
        public DataTable readProductoras()
        {
            SqlConnection connection = null;
            DataTable productoras = new DataTable();

            try
            {
                connection = new SqlConnection(datos);
                connection.Open();

                string sentence = "SELECT * FROM Productora";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(productoras);


            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading productoras operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return productoras;
        }

    }
}
