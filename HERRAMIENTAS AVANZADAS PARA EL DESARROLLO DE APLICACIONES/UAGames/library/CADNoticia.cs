using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace library
{
    class CADNoticia
    {
        private string cadenaConexion;

        public CADNoticia()
        {

            cadenaConexion = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
        }
        //no
        public bool createNoticia(ENNoticia en)
        {
            bool creada = false;
            SqlConnection conect = null;
            try
            {
                conect = new SqlConnection(cadenaConexion);
                conect.Open();
                string fechaFormatoCorrecto = en.FechaPublicacion.ToString("yyyy-MM-dd");
                string query = "INSERT INTO [Noticia] " +
                "(titulo, contenido, fecha_public, imagen, productoraID) " +
                "VALUES ('"+en.Titulo+"', '"+en.Contenido+"','" + fechaFormatoCorrecto +"','"+en.Imagen+"',"+en.ProductoraID+");";
                SqlCommand consulta = new SqlCommand(query, conect);
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


        //si

        public DataTable readNoticiass()
        {
            SqlConnection connection = null;
            DataTable noticia = new DataTable();

            try
            {
                connection = new SqlConnection(cadenaConexion);
                connection.Open();

                string sentence = "SELECT * FROM Noticia";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(noticia);


            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading noticias operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading noticias operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return noticia;
        }

    



    //si
    public bool updateNoticia(ENNoticia en)
        {
            bool updated = false;
            SqlConnection con = new SqlConnection(cadenaConexion);
            try
            {
                
                con.Open();
                string query = "UPDATE Noticia SET titulo=@titulo,contenido=@contenido, imagen=@imagen, fecha_public=@fecha_public WHERE Id=@Id";
                SqlCommand consulta = new SqlCommand(query, con);
                consulta.Parameters.AddWithValue("@contenido", en.Contenido);
                consulta.Parameters.AddWithValue("@fecha_public", en.FechaPublicacion);
                consulta.Parameters.AddWithValue("@imagen", en.Imagen);
                consulta.Parameters.AddWithValue("@titulo", en.Titulo);
                consulta.Parameters.AddWithValue("@id", en.Id);
                consulta.ExecuteNonQuery();
                updated = true;
            }
            catch (Exception ex)
            {
                throw new Exception("Error al actualizar la noticia en la base de datos: " + ex.Message);
            }
            finally
            {
                if (con != null)
                    con.Close();

            }
            return updated;




        }
        //si
        public bool deleteNoticia(ENNoticia en)
        {
            bool del = false;
            SqlConnection con = new SqlConnection(cadenaConexion);
            try
            {
               
                con.Open();
                string query = "DELETE FROM Noticia WHERE Id=@Id";
                SqlCommand consulta = new SqlCommand(query, con);
                consulta.Parameters.AddWithValue("@Id", en.Id);
                consulta.ExecuteNonQuery();
                del = true;
            }
            catch (Exception ex)
            {
                throw new Exception("Error al eliminar la noticia de la base de datos: " + ex.Message);
            }
            finally
            {
                if (con != null)
                    con.Close();
            }
            return del;
        }
        public DataSet readNoticia()
        {
            DataSet bdvirtual = new DataSet();

            SqlConnection c = new SqlConnection(cadenaConexion);
            string query = "select titulo,fecha_public,contenido FROM Noticia ORDER by fecha_public desc";
            SqlDataAdapter da = new SqlDataAdapter(query, c);
            da.Fill(bdvirtual, "Noticia");
            return bdvirtual;


        }
        //si
        public DataTable readNoticiasId2(ENNoticia en)
        {
            SqlConnection connection = null;
            DataTable noticias = new DataTable();

            try
            {
                connection = new SqlConnection(cadenaConexion);
                connection.Open();

                string sentence = "SELECT titulo , fecha_public , contenido , imagen , id FROM [Noticia] where id='" + en.Id + "';";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(noticias);


            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading noticias operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading noticias operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return noticias;
        }
        //si
        public bool readNoticia(ENNoticia en)
        {
            bool leida = false;
            SqlConnection con = new SqlConnection(cadenaConexion);

            try
            {
                
                con.Open();
                string query = "Select * From Noticia Where Id='" + en.Id + "' ";
                SqlCommand consulta = new SqlCommand(query, con);
                SqlDataReader search = consulta.ExecuteReader();


                if (search.Read())
                {
                    en.Id = Int32.Parse(search["Id"].ToString());
                    en.Titulo = search["Nombre"].ToString();
                    en.Contenido = search["Contenido"].ToString();
                    en.Imagen = search["Imagen"].ToString();
                    en.FechaPublicacion = DateTime.Parse(search["fecha_public"].ToString());
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
        //si
        public DataTable readNoticiaTitulo2(ENNoticia en)
        {
            SqlConnection connection = null;
            DataTable noticia = new DataTable();

            try
            {
                connection = new SqlConnection(cadenaConexion);
                connection.Open();

                string sentence = "SELECT * FROM [Noticia] where titulo='" + en.Titulo + "';";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(noticia);


            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading noticia operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading noticia operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return noticia;
        }
    }
}
