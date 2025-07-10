using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;

namespace library
{
    public class CADVideojuego
    {
        private String constring;

        public CADVideojuego()
        {
            constring = ConfigurationManager.ConnectionStrings["miconexion"].ToString();
        }

        //Crear videojuego
        public bool addVideojuego(ENVideojuego en)
        {
            bool creado = false;
            SqlConnection connection = null;
            /*String sentence = "INSERT INTO [Videojuego] " +
                "(titulo, descripcion, fecha_lanzamiento, plataforma, precio, imagen, productoraID, categoriaID) " +
                "VALUES (@titulo, @descripcion, @fecha_lanzamiento, @plataforma, @precio, @imagen, @productoraID, @categoriaID);";*/


            try
            {
                CADProductora productora = new CADProductora();
                productora.readProductora(en.Productora);

                CADCategoria categoria = new CADCategoria();
                categoria.readCategoria(en.Categoria);


                String sentence = "INSERT INTO [Videojuego] (titulo, descripcion, fecha_lanzamiento, plataforma, precio, productoraID, categoriaID) " +
                    "VALUES ('" + en.Titulo + "', '" + en.Descripcion  + "', '" + en.FechaLanzamiento.ToString("yyyy/MM/dd") + "', '" + en.Plataforma
                + "', " + en.Precio + ", "  + en.Productora.Id + ", " + en.Categoria.id
                + " );";

                connection = new SqlConnection(constring);
                connection.Open();

                SqlCommand com = new SqlCommand(sentence, connection);

                /*com.Parameters.AddWithValue("@titulo", en.Titulo);
                com.Parameters.AddWithValue("@descripcion", en.Descripcion);
                com.Parameters.AddWithValue("@fecha_lanzamiento", en.FechaLanzamiento.ToString("yyyy-MM-dd"));
                com.Parameters.AddWithValue("@plataforma", en.Plataforma);
                com.Parameters.AddWithValue("@precio", en.Precio);
                com.Parameters.AddWithValue("@imagen", en.Imagen);
                com.Parameters.AddWithValue("@productoraID", en.Productora.Id);
                com.Parameters.AddWithValue("@categoriaID", en.Categoria.id);*/

                com.ExecuteNonQuery();
                creado = true;
            }
            catch (SqlException e)
            {
                creado = false;
                Console.WriteLine("Creating videogame operation has failed.Error: {0}", e.Message);
            }
            catch (Exception e)
            {
                creado = false;
                Console.WriteLine("Creating videogame operation has failed.Error: {0}", e.Message);
            }
            finally
            {
                if (connection != null)
                {
                    connection.Close();
                }
            }
            return creado;

        }

        //Leer videojuego
        public bool readVideojuego(ENVideojuego en)
        {
            bool leido = false;
            SqlConnection connection = null;
            SqlDataReader dr = null;
            try
            {
                connection = new SqlConnection(constring);
                connection.Open();
                string sentence = "SELECT * FROM [Videojuego] WHERE titulo = @titulo";
                SqlCommand com = new SqlCommand(sentence, connection);
                com.Parameters.AddWithValue("@titulo", en.Titulo);
                //Se obtiene cursor
                dr = com.ExecuteReader();

                //Se lee primer elemento
                if (dr.Read())
                {
                    en.Id = Int32.Parse(dr["id"].ToString());
                    en.Descripcion = dr["descripcion"].ToString();
                    en.FechaLanzamiento = DateTime.Parse(dr["fecha_lanzamiento"].ToString());
                    en.Plataforma = dr["plataforma"].ToString();
                    en.Imagen = dr["imagen"].ToString();
                    en.Precio = Double.Parse(dr["precio"].ToString());

                    //Lectura de la productora
                    ENProductora productora = new ENProductora();
                    productora.Id = Int32.Parse(dr["productoraID"].ToString());

                    CADProductora prod = new CADProductora();
                    prod.readProductora(productora);

                    en.Productora = productora;

                    //Lectura de la categoria
                    ENCategoria cat = new ENCategoria();
                    cat.id = Int32.Parse(dr["categoriaID"].ToString());

                    CADCategoria categoria = new CADCategoria();
                    categoria.readCategoria(cat);

                    en.Categoria = cat;

                    leido = true;
                }

            }
            catch (SqlException sqlex)
            {
                leido = false;
                Console.WriteLine("Reading videojuego operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                leido = false;
                Console.WriteLine("Reading videojuego operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (dr != null) dr.Close();
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return leido;
        }


        public bool readVideojuegoId(ENVideojuego en)
        {
            bool leido = false;
            SqlConnection connection = null;
            SqlDataReader dr = null;
            try
            {
                connection = new SqlConnection(constring);
                connection.Open();
                string sentence = "SELECT * FROM [Videojuego] WHERE Id = @Id";
                SqlCommand com = new SqlCommand(sentence, connection);
                com.Parameters.AddWithValue("@Id", en.Id);
                //Se obtiene cursor
                dr = com.ExecuteReader();

                //Se lee primer elemento
                if (dr.Read())
                {
                    en.Titulo = dr["titulo"].ToString();
                    en.Descripcion = dr["descripcion"].ToString();
                    en.FechaLanzamiento = DateTime.Parse(dr["fecha_lanzamiento"].ToString());
                    en.Plataforma = dr["plataforma"].ToString();
                    en.Imagen = dr["imagen"].ToString();
                    en.Precio = Double.Parse(dr["precio"].ToString());

                    //Lectura de la productora
                    ENProductora productora = new ENProductora();
                    productora.Id = Int32.Parse(dr["productoraID"].ToString());

                    CADProductora prod = new CADProductora();
                    prod.readProductora(productora);

                    en.Productora = productora;

                    //Lectura de la categoria
                    ENCategoria cat = new ENCategoria();
                    cat.id = Int32.Parse(dr["categoriaID"].ToString());

                    CADCategoria categoria = new CADCategoria();
                    categoria.readCategoria(cat);

                    en.Categoria = cat;

                    leido = true;
                }

            }
            catch (SqlException sqlex)
            {
                leido = false;
                Console.WriteLine("Reading videojuego operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                leido = false;
                Console.WriteLine("Reading videojuego operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (dr != null) dr.Close();
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return leido;
        }

        //Leer todos los videojuegos
        public DataTable readVideojuegos()
        {
            SqlConnection connection = null;
            DataTable videojuegos = new DataTable();

            try
            {
                connection = new SqlConnection(constring);
                connection.Open();

                string sentence = "SELECT v.titulo, v.id, v.descripcion, v.fecha_lanzamiento, v.plataforma, v.precio, v.imagen, " +
                    "o.descuento, o.fecha_inicio, o.fecha_fin, CAST((v.precio*(100- COALESCE(o.descuento, 0))/100) AS DECIMAL(5,2)) as nuevoPrecio, " +
                    "p.nombre AS productora, c.nombre AS categoria FROM [Videojuego] v " +
                    "JOIN [Productora] p ON v.productoraID = p.id " +
                    "JOIN [Categoria] c ON v.categoriaID = c.id " +
                    "LEFT JOIN (SELECT * FROM [Oferta] WHERE GETDATE() BETWEEN fecha_inicio AND fecha_fin) o ON o.videojuegoID = v.id " +
                    "ORDER BY v.id;";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(videojuegos);
               
            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading videojuegos operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading videojuegos operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return videojuegos;
        }


        public DataTable readVideojuegos(string query)
        {
            SqlConnection connection = null;
            DataTable videojuegos = new DataTable();

            try
            {
                connection = new SqlConnection(constring);
                connection.Open();

                string sentence = "SELECT v.titulo, v.id, v.descripcion, v.fecha_lanzamiento, v.plataforma, v.precio, v.imagen, " +
                    "o.descuento, o.fecha_inicio, o.fecha_fin, CAST((v.precio*(100- COALESCE(o.descuento, 0))/100) AS DECIMAL(5,2)) as nuevoPrecio, " +
                    "p.nombre AS productora, c.nombre AS categoria FROM [Videojuego] v " +
                    "JOIN [Productora] p ON v.productoraID = p.id " +
                    "JOIN [Categoria] c ON v.categoriaID = c.id " +
                    "LEFT JOIN (SELECT * FROM [Oferta] WHERE GETDATE() BETWEEN fecha_inicio AND fecha_fin) o ON o.videojuegoID = v.id ";
                sentence += query;
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(videojuegos);

            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading videojuegos operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading videojuegos operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return videojuegos;
        }

        //Leer videojuegos especificos de una productora
        public DataTable readVideojuegosProductora(ENVideojuego en)
        {
            SqlConnection connection = null;
            DataTable videojuegos = new DataTable();

            try
            {
                connection = new SqlConnection(constring);
                connection.Open();

                string sentence = "SELECT v.id, v.titulo, v.descripcion, v.fecha_lanzamiento, v.plataforma, v.precio, v.imagen, " +
                    "p.nombre AS productora, c.nombre AS categoria FROM [Videojuego] v " +
                    "JOIN [Productora] p ON v.productoraID = p.id " +
                    "JOIN [Categoria] c ON v.categoriaID = c.id " +
                    "WHERE v.productoraID = '" + en.Productora.Id +
                    "';";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(videojuegos);

            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading videojuegos and productora operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading videojuegos and productora operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return videojuegos;
        }

        //Leer videojuegos especificos de una categoria
        public DataTable readVideojuegosCategoria(ENVideojuego en)
        {
            SqlConnection connection = null;
            SqlDataReader dr = null;
            DataTable videojuegos = new DataTable();

            try
            {
                connection = new SqlConnection(constring);
                connection.Open();

                string sentence = "SELECT v.id, v.titulo, v.descripcion, v.fecha_lanzamiento, v.plataforma, v.precio, v.imagen, " +
                    "p.nombre AS productora, c.nombre AS categoria FROM [Videojuego] v " +
                    "JOIN [Productora] p ON v.productoraID = p.id " +
                    "JOIN [Categoria] c ON v.categoriaID = c.id " +
                    "WHERE v.categoriaID = '" + en.Categoria.id +
                    "';";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(videojuegos);

            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Reading videojuegos and categoria operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Reading videojuegos and categoria operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (dr != null) dr.Close();
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return videojuegos;
        }



        //Actualizar videojuego
        public bool updateVideojuego(ENVideojuego en)
        {
            bool actualizado = false;
            SqlConnection connection = null;

            

            try
            {
                CADProductora productora = new CADProductora();
                productora.readProductoraNombre(en.Productora);

                CADCategoria categoria = new CADCategoria();
                categoria.readCategoriaNombre(en.Categoria);

                String sentence = "UPDATE [Videojuego] SET titulo='" + en.Titulo + "', descripcion='" + en.Descripcion
                + "', fecha_lanzamiento='" + en.FechaLanzamiento.ToString("yyyy/MM/dd") + "', plataforma='" + en.Plataforma
                + "', precio='" + en.Precio + "', imagen='" + en.Imagen + "', productoraID='" + en.Productora.Id + "', categoriaID='" + en.Categoria.id
                + "' WHERE id = '" + en.Id + "'";

                /*String sentence = "UPDATE [Videojuego] SET " +
                "titulo = @titulo, descripcion = @descripcion, fecha_lanzamiento = @fecha_lanzamiento," +
                "plataforma = @plataforma, precio = @precio, imagen = @imagen, productoraID = @productoraID, categoriaID = @categoriaID " +
                "WHERE id = @id;";*/


                connection = new SqlConnection(constring);
                connection.Open();

                SqlCommand com = new SqlCommand(sentence, connection);

                /*com.Parameters.AddWithValue("@titulo", en.Titulo);
                com.Parameters.AddWithValue("@descripcion", en.Descripcion);
                com.Parameters.AddWithValue("@fecha_lanzamiento", en.FechaLanzamiento.ToString("yyyy-MM-dd"));
                com.Parameters.AddWithValue("@plataforma", en.Plataforma);
                com.Parameters.AddWithValue("@precio", en.Precio);
                com.Parameters.AddWithValue("@imagen", en.Imagen);
                com.Parameters.AddWithValue("@productoraID", en.Productora.Id);
                com.Parameters.AddWithValue("@categoriaID", en.Categoria);
                com.Parameters.AddWithValue("@id", en.Id);*/

                com.ExecuteNonQuery();

                actualizado = true;
            }
            catch (SqlException e)
            {
                actualizado = false;
                Console.WriteLine("Updating videogame operation has failed.Error: {0}", e.Message);
            }
            catch (Exception e)
            {
                actualizado = false;
                Console.WriteLine("Updating videogame operation has failed.Error: {0}", e.Message);
            }
            finally
            {
                if (connection != null)
                {
                    connection.Close();
                }
            }
            return actualizado;

        }

        //Actualizar videojuego en modo desconectado
        public DataTable updateVideojuego(ENVideojuego en, int i)
        {
            SqlConnection connection = null;
            DataTable videojuegos = new DataTable();

            try
            {
                connection = new SqlConnection(constring);
                connection.Open();

                string sentence = " ";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(videojuegos);

                videojuegos.Rows[i]["titulo"] = en.Titulo;
                videojuegos.Rows[i]["descripcion"] = en.Descripcion;
                videojuegos.Rows[i]["plataforma"] = en.Plataforma;
                videojuegos.Rows[i]["precio"] = en.Precio;
                videojuegos.Rows[i]["imagen"] = en.Imagen;
                videojuegos.Rows[i]["fecha_lazamiento"] = en.FechaLanzamiento.ToString("yyyy-MM-dd");
                videojuegos.Rows[i]["titulo"] = en.Titulo;


                SqlCommandBuilder cbuilder = new SqlCommandBuilder(adapter);
                adapter.Update(videojuegos);

                return videojuegos;


            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Updating videojuego operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Updating videojuego operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return videojuegos;
        }


        //Eliminar videojuego en modo desconectado
        public DataTable deleteVideojuego(ENVideojuego en, int i)
        {
            SqlConnection connection = null;
            DataTable videojuegos = new DataTable();

            try
            {
                connection = new SqlConnection(constring);
                connection.Open();

                string sentence = " ";
                SqlDataAdapter adapter = new SqlDataAdapter(sentence, connection);
                adapter.Fill(videojuegos);

                videojuegos.Rows[i]["titulo"] = en.Titulo;
                videojuegos.Rows[i]["descripcion"] = en.Descripcion;
                videojuegos.Rows[i]["plataforma"] = en.Plataforma;
                videojuegos.Rows[i]["precio"] = en.Precio;
                videojuegos.Rows[i]["imagen"] = en.Imagen;
                videojuegos.Rows[i]["fecha_lazamiento"] = en.FechaLanzamiento.ToString("yyyy-MM-dd");
                videojuegos.Rows[i]["titulo"] = en.Titulo;


                SqlCommandBuilder cbuilder = new SqlCommandBuilder(adapter);
                adapter.Update(videojuegos);

                return videojuegos;


            }
            catch (SqlException sqlex)
            {
                Console.WriteLine("Updating videojuego operation has failed.Error: {0}", sqlex.Message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Updating videojuego operation has failed.Error: {0}", ex.Message);
            }
            finally
            {
                if (connection != null) connection.Close(); // Se asegura de cerrar la conexión.
            }
            return videojuegos;
        }


        //Eliminar videojuego
        public bool deleteVideojuego(ENVideojuego en)
        {
            bool eliminado = false;
            SqlConnection connection = null;

            try
            {
                connection = new SqlConnection(constring);
                connection.Open();

                string sentence = "DELETE FROM [Videojuego] " +
                    "where id = @id;";
                SqlCommand com = new SqlCommand(sentence, connection);
                com.Parameters.AddWithValue("@id", en.Id);
                com.ExecuteNonQuery();

                eliminado = true;
            }
            catch (SqlException e)
            {
                eliminado = false;
                Console.WriteLine("Deleting videogame operation has failed.Error: {0}", e.Message);
            }
            catch (Exception e)
            {
                eliminado = false;
                Console.WriteLine("Deleting videogame operation has failed.Error: {0}", e.Message);
            }
            finally
            {
                if (connection != null)
                {
                    connection.Close();
                }
            }
            return eliminado;
        }


    }

}
