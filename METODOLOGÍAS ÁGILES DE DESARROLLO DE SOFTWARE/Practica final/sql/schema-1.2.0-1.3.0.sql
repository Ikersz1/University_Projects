ALTER TABLE public.equipos
	ADD COLUMN creador_id bigint;

ALTER TABLE public.equipos
	ADD CONSTRAINT creador FOREIGN KEY (creador_id) REFERENCES public.usuarios(id);


