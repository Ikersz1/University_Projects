package madstodolist.service;

import ch.qos.logback.classic.Logger;
import madstodolist.dto.EquipoData;
import madstodolist.dto.TareaData;
import madstodolist.dto.UsuarioData;
import madstodolist.model.Equipo;
import madstodolist.model.Tarea;
import madstodolist.model.Usuario;
import madstodolist.repository.EquipoRepository;
import madstodolist.repository.TareaRepository;
import madstodolist.repository.UsuarioRepository;
import org.modelmapper.ModelMapper;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.domain.Sort;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.List;
import java.util.stream.Collectors;

@Service
public class EquipoService {

    @Autowired
    EquipoRepository equipoRepository;

    @Autowired
    private ModelMapper modelMapper;

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private TareaRepository tareaRepository;

    @Transactional
    public EquipoData crearEquipo(String nombre) {
        if (nombre == null || nombre.trim().isEmpty()) {
            throw new EquipoServiceException("El nombre del equipo no puede estar vacío");
        }

        // Creamos el equipo y lo guardamos en la base de datos
        Equipo equipo = new Equipo(nombre);
        equipo.setCreador("creador_anonimo");
        equipoRepository.save(equipo);

        // Convertimos el equipo a EquipoData para devolverlo
        return modelMapper.map(equipo, EquipoData.class);
    }



    public List<EquipoData> findAllOrdenadoPorNombre() {
        List<Equipo> equipos = equipoRepository.findAllByOrderByNombreAsc();
        return equipos.stream()
                .map(equipo -> modelMapper.map(equipo, EquipoData.class))
                .collect(Collectors.toList());
    }
    @Transactional
    public EquipoData recuperarEquipo(Long id) {
        Equipo equipo = equipoRepository.findById(id)
                .orElseThrow(() -> new EquipoServiceException("Equipo no encontrado con id: " + id));
        return modelMapper.map(equipo, EquipoData.class);
    }

    @Transactional
    public void añadirUsuarioAEquipo(Long equipoId, Long usuarioId) {
        Equipo equipo = equipoRepository.findById(equipoId)
                .orElseThrow(() -> new EquipoServiceException("Equipo no encontrado"));
        Usuario usuario = usuarioRepository.findById(usuarioId)
                .orElseThrow(() -> new EquipoServiceException("Usuario no encontrado"));

        // Capturamos el ID del usuario en una variable local (final o efectivamente final)
        final Long idUsuario = usuario.getId();

        // Comprobar si el usuario ya pertenece al equipo
        if (equipo.getUsuarios().stream().anyMatch(u -> u.getId().equals(idUsuario))) {
            throw new EquipoServiceException("El usuario ya pertenece al equipo");
        }

        // Añadir el usuario al equipo y viceversa
        equipo.addUsuario(usuario);
        usuario.addEquipo(equipo);
    }


    @Transactional(readOnly = true)
    public List<UsuarioData> usuariosEquipo(Long equipoId) {
        Equipo equipo = equipoRepository.findById(equipoId)
                .orElseThrow(() -> new EquipoServiceException("Equipo no encontrado con id: " + equipoId));
        return equipo.getUsuarios().stream()
                .map(usuario -> modelMapper.map(usuario, UsuarioData.class))
                .collect(Collectors.toList());
    }

    @Transactional(readOnly = true)
    public List<EquipoData> equiposUsuario(Long usuarioId) {
        Usuario usuario = usuarioRepository.findById(usuarioId)
                .orElseThrow(() -> new EquipoServiceException("Usuario no encontrado con id: " + usuarioId));
        return usuario.getEquipos().stream()
                .map(equipo -> modelMapper.map(equipo, EquipoData.class))
                .collect(Collectors.toList());
    }

    @Transactional
    public void eliminarUsuarioDeEquipo(Long equipoId, Long usuarioId) {
        // Recuperamos el equipo y el usuario desde la base de datos
        Equipo equipo = equipoRepository.findById(equipoId).orElseThrow(() ->
                new EquipoServiceException("Equipo no encontrado"));
        Usuario usuario = usuarioRepository.findById(usuarioId).orElseThrow(() ->
                new EquipoServiceException("Usuario no encontrado"));

        // Verificamos si el usuario pertenece al equipo
        if (!equipo.getUsuarios().contains(usuario)) {
            throw new EquipoServiceException("El usuario no pertenece al equipo");
        }

        // Eliminamos la relación en ambos lados
        equipo.getUsuarios().remove(usuario);
        usuario.getEquipos().remove(equipo);

        // Guardamos los cambios en la base de datos
        equipoRepository.save(equipo);
        usuarioRepository.save(usuario);
    }

    public void renombrarEquipo(Long equipoId, String nuevoNombre) {
        if (nuevoNombre == null || nuevoNombre.trim().isEmpty()) {
            throw new EquipoServiceException("El nombre del equipo no puede estar vacío.");
        }

        Equipo equipo = equipoRepository.findById(equipoId)
                .orElseThrow(() -> new EquipoServiceException("El equipo no existe."));

        // Validación para evitar nombres duplicados
        boolean nombreDuplicado = equipoRepository.findByNombre(nuevoNombre).isPresent();
        if (nombreDuplicado && !equipo.getNombre().equals(nuevoNombre)) {
            throw new EquipoServiceException("Ya existe un equipo con este nombre.");
        }

        equipo.setNombre(nuevoNombre);
        equipoRepository.save(equipo);
    }


    public void eliminarEquipo(Long equipoId) {
        Equipo equipo = equipoRepository.findById(equipoId)
                .orElseThrow(() -> new EquipoServiceException("El equipo no existe."));

        equipoRepository.delete(equipo);
    }







    @Transactional
    public EquipoData crearEquipoConCreador(String nombre, Long usuarioId) {
        if (nombre == null || nombre.trim().isEmpty()) {
            throw new EquipoServiceException("El nombre del equipo no puede estar vacío.");
        }

        Usuario usuario = usuarioRepository.findById(usuarioId)
                .orElseThrow(() -> new EquipoServiceException("Usuario no encontrado"));

        // Creamos el equipo y asignamos la fecha automáticamente
        Equipo equipo = new Equipo(nombre);
        equipo.setCreador(usuario.getEmail());
        equipoRepository.save(equipo);

        return modelMapper.map(equipo, EquipoData.class);
    }

    @Transactional(readOnly = true)
    public List<TareaData> listarTareasDeEquipo(Long equipoId) {
        Equipo equipo = equipoRepository.findById(equipoId)
                .orElseThrow(() -> new EquipoServiceException("Equipo no encontrado con id: " + equipoId));
        return equipo.getTareas().stream()
                .map(tarea -> modelMapper.map(tarea, TareaData.class))
                .collect(Collectors.toList());
    }


    @Transactional
    public TareaData crearTareaParaEquipo(Long equipoId, String titulo, String prioridad) {
        if (titulo == null || titulo.trim().isEmpty()) {
            throw new EquipoServiceException("El título de la tarea no puede estar vacío");
        }

        Equipo equipo = equipoRepository.findById(equipoId)
                .orElseThrow(() -> new EquipoServiceException("Equipo no encontrado con id: " + equipoId));

        Tarea tarea = new Tarea();
        tarea.setTitulo(titulo);
        tarea.setPrioridad(prioridad != null ? prioridad : "BAJA");
        tarea.setEquipo(equipo);

        tareaRepository.save(tarea);

        return modelMapper.map(tarea, TareaData.class);
    }

    @Transactional
    public void eliminarTareaDeEquipo(Long tareaId) {
        Tarea tarea = tareaRepository.findById(tareaId)
                .orElseThrow(() -> new EquipoServiceException("Tarea no encontrada con id: " + tareaId));
        tareaRepository.delete(tarea);
    }

    @Transactional(readOnly = true)
    public EquipoData findById(Long idEquipo) {

        // Buscar equipo por ID, devolviendo null si no se encuentra
        Equipo equipo = equipoRepository.findById(idEquipo).orElse(null);

        if (equipo == null) return null;
        else {
            // Usar modelMapper para convertir la entidad Equipo a EquipoData
            return modelMapper.map(equipo, EquipoData.class);
        }
    }



}