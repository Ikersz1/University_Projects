
**Documentación practica 4.**


Se ha añadido un campo llamado creador al modelo y al DTO de Equipo. Este campo es un String 
que almacena el nombre del usuario que crea el equipo, permitiendo mostrar esta información de 
manera sencilla en las vistas relacionadas.

2. Cambios realizados

   **Modelo Equipo:**
   Se añadió el atributo creador.
   Se actualizaron los métodos necesarios para incluir este campo.

   **DTO EquipoData:**
   Se incorporó el atributo creador.
   Se implementaron los métodos *getCreador* y *setCreador* para permitir el acceso y la modificación del campo.
   
   **Servicios EquipoService:**
   Se creó el servicio *crearEquipoConCreador*, este se encarga de crear un equipo como ya hacía el servicio *crearEquipo*, pero con el agregado de guardar el nombre del usuario creador, obteniendolo a traves del identificador de usuario pasado por parámetro.
   
   **Controlador crearNuevoEquipo:**
   Se modificó el controlador POST, para que llame al servicio *crearEquipoConCreador* en vez de al servicio *crearEquipo*. Ahora obtiene el identificador del usuario autenticado gracias a la función *managerUserSession.usuarioLogeado* y se lo pasa al servicio como parámetro.
   
   **Test EquipoServiceTest:**
   Se añadió el test *crearEquipoUsuarioAnonimo*, este verifica que al crear un equipo con la funcion *crearEquipo*, el creador asignado automáticamente sea "creador_anonimo"
   Se añadió el test *crearEquipoUsuarioNoAnonimo*, que verifica que al crear un equipo con la función *crearEquipoconCreador*, el creador sea el nombre del usuario pasado como parámetro.

   ### Visualización del creador del equipo

   En las vistas se ha añadido la capacidad de mostrar quién es el creador de un equipo. Esto se implementa mediante el campo `creador` del modelo de equipo,

   Modelo Equipo:
   Se añadió el atributo creador.
   Se actualizaron los métodos necesarios para incluir este campo.

   DTO EquipoData:
   Se incorporó el atributo creador.
   Se implementaron los métodos getCreador y setCreador para permitir el acceso y la modificación del campo.

   ### Visualización del creador del equipo

   En las vistas se ha añadido la capacidad de mostrar quién es el creador de un equipo. Esto se implementa mediante el campo `creador` del modelo de equipo, que se renderiza directamente en la plantilla correspondiente.

# Esquemas de datos de las versiones 1.2.0 y 1.3.0

## Cambios en el esquema de la base de datos

### Versión 1.2.0
El esquema de la versión 1.2.0 contiene las siguientes tablas principales:
- **usuarios**: Almacena información sobre los usuarios, como su nombre, email y estado de administrador o bloqueado.
- **equipos**: Define los equipos con un campo `nombre` para identificarlos.
- **tareas**: Representa las tareas asignadas a los usuarios mediante el campo `usuario_id`.
- **equipo_usuario**: Tabla intermedia para la relación muchos-a-muchos entre `equipos` y `usuarios`.

### Versión 1.3.0
En la versión 1.3.0 se introduce un cambio importante en la tabla `equipos`:
- Se añade el campo **`creador_id`** que apunta al usuario que creó el equipo. Esto permite identificar quién es el responsable original de cada equipo.
- Se establece una clave foránea (`FOREIGN KEY`) que conecta `creador_id` con la tabla `usuarios`.

### Comparativa de cambios
#### Tablas modificadas:
1. **`equipos`**:
    - **Antes (1.2.0):**
      ```sql
      CREATE TABLE public.equipos (
          id bigint NOT NULL,
          nombre character varying(255)
      );
      ```
    - **Después (1.3.0):**
      ```sql
      CREATE TABLE public.equipos (
          id bigint NOT NULL,
          nombre character varying(255),
          creador_id bigint
      );
      ```
    - **Nuevo índice y clave foránea:**
      ```sql
      ALTER TABLE ONLY public.equipos
          ADD CONSTRAINT fknl2mvlmjq2s3tcbej6srtf9f8 FOREIGN KEY (creador_id) REFERENCES public.usuarios(id);
      ```

### Resumen de cambios
- **Campo añadido:** `creador_id` en la tabla `equipos`.
- **Nueva clave foránea:** `creador_id` apunta a `usuarios.id`.

Este cambio se realizó para permitir que cada equipo registre información sobre su creador, haciendo más completo el modelo de datos.

### Ejemplo de uso
En la vista de detalle de los equipos, ahora se puede mostrar el nombre del creador del equipo utilizando el campo `creador_id`. Esto aporta mayor contexto y transparencia a los datos presentados.



**URL Dockerhub**
https://hub.docker.com/r/guu1/mads-todolist-equipo12
