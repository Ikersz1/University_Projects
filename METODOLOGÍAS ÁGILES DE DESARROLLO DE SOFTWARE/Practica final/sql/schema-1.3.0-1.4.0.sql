-- Modificación en el archivo schema-1.4.0.sql para reflejar los cambios en la estructura de la tabla `equipos`

-- 1. Modificar la columna `creador` y `nombre`
ALTER TABLE public.equipos
    DROP COLUMN creador,  -- Eliminar la columna `creador` de tipo `character varying`
    ADD COLUMN creador_id bigint;  -- Añadir la columna `creador_id` de tipo `bigint`

-- 2. Crear la relación con la tabla `usuarios` mediante la clave foránea
ALTER TABLE ONLY public.equipos
    ADD CONSTRAINT fknl2mvlmjq2s3tcbej6srtf9f8 FOREIGN KEY (creador_id)
    REFERENCES public.usuarios(id);  -- Clave foránea que hace referencia a la tabla `usuarios`

