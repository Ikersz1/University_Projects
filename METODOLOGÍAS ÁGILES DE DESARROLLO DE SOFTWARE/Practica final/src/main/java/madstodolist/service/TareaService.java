package madstodolist.service;

import madstodolist.dto.SubtareaData;
import madstodolist.model.Equipo;
import madstodolist.model.Subtarea;
import madstodolist.model.Tarea;
import madstodolist.repository.EquipoRepository;
import madstodolist.repository.SubtareaRepository;
import madstodolist.repository.TareaRepository;
import madstodolist.model.Usuario;
import madstodolist.repository.UsuarioRepository;
import madstodolist.dto.TareaData;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.modelmapper.ModelMapper;

import java.util.Collections;
import java.util.List;

import java.util.stream.Collectors;


@Service
public class TareaService {

    Logger logger = LoggerFactory.getLogger(TareaService.class);

    @Autowired
    private UsuarioRepository usuarioRepository;
    @Autowired
    private TareaRepository tareaRepository;
    @Autowired
    private ModelMapper modelMapper;
    @Autowired
    private SubtareaRepository subtareaRepository; // Nuevo repositorio
    @Autowired
    private EquipoRepository equipoRepository; // Nuevo repositorio

    @Transactional
    public TareaData nuevaTareaUsuario(Long idUsuario, String tituloTarea) {
        logger.debug("Añadiendo tarea " + tituloTarea + " al usuario " + idUsuario);
        Usuario usuario = usuarioRepository.findById(idUsuario).orElse(null);
        if (usuario == null) {
            throw new TareaServiceException("Usuario " + idUsuario + " no existe al crear tarea " + tituloTarea);
        }
        Tarea tarea = new Tarea(usuario, tituloTarea);
        tareaRepository.save(tarea);
        return modelMapper.map(tarea, TareaData.class);
    }

    public void nuevaTareaEquipo(Long idEquipo, String titulo) {
        Equipo equipo = equipoRepository.findById(idEquipo)
                .orElseThrow(() -> new IllegalArgumentException("Equipo no encontrado"));

        Tarea tarea = new Tarea(equipo, titulo);
        equipo.addTarea(tarea);
        equipoRepository.save(equipo);
        tareaRepository.save(tarea);
    }


    @Transactional(readOnly = true)
    public List<TareaData> allTareasUsuario(Long idUsuario) {
        logger.debug("Devolviendo todas las tareas del usuario " + idUsuario);
        Usuario usuario = usuarioRepository.findById(idUsuario).orElse(null);
        if (usuario == null) {
            throw new TareaServiceException("Usuario " + idUsuario + " no existe al listar tareas ");
        }
        // Hacemos uso de Java Stream API para mapear la lista de entidades a DTOs.
        List<TareaData> tareas = usuario.getTareas().stream()
                .map(tarea -> modelMapper.map(tarea, TareaData.class))
                .collect(Collectors.toList());
        // Ordenamos la lista por id de tarea
        Collections.sort(tareas, (a, b) -> a.getId() < b.getId() ? -1 : a.getId() == b.getId() ? 0 : 1);
        return tareas;
    }

    @Transactional(readOnly = true)
    public TareaData findById(Long tareaId) {
        logger.debug("Buscando tarea " + tareaId);
        Tarea tarea = tareaRepository.findById(tareaId).orElse(null);
        if (tarea == null) return null;
        else return modelMapper.map(tarea, TareaData.class);
    }

    @Transactional
    public TareaData modificaTarea(Long idTarea, String nuevoTitulo) {
        logger.debug("Modificando tarea " + idTarea + " - " + nuevoTitulo);
        Tarea tarea = tareaRepository.findById(idTarea).orElse(null);
        if (tarea == null) {
            throw new TareaServiceException("No existe tarea con id " + idTarea);
        }
        tarea.setTitulo(nuevoTitulo);
        tarea = tareaRepository.save(tarea);
        return modelMapper.map(tarea, TareaData.class);
    }

    @Transactional
    public void borraTarea(Long idTarea) {
        logger.debug("Borrando tarea " + idTarea);
        Tarea tarea = tareaRepository.findById(idTarea).orElse(null);
        if (tarea == null) {
            throw new TareaServiceException("No existe tarea con id " + idTarea);
        }
        tareaRepository.delete(tarea);
    }

    @Transactional
    public boolean usuarioContieneTarea(Long usuarioId, Long tareaId) {
        Tarea tarea = tareaRepository.findById(tareaId).orElse(null);
        Usuario usuario = usuarioRepository.findById(usuarioId).orElse(null);
        if (tarea == null || usuario == null) {
            throw new TareaServiceException("No existe tarea o usuario id");
        }
        return usuario.getTareas().contains(tarea);
    }


    @Transactional
    public void destacarTarea(Long idTarea, boolean destacar) {
        Tarea tarea = tareaRepository.findById(idTarea).orElse(null);
        if (tarea == null) {
            throw new TareaServiceException("No existe tarea con id " + idTarea);
        }
        tarea.setDestacado(destacar);
        tareaRepository.save(tarea);
    }

    @Transactional
    public SubtareaData crearSubtarea(Long idTarea, String nombreSubtarea) {
        logger.debug("Creando subtarea '" + nombreSubtarea + "' en la tarea " + idTarea);

        // Buscar la tarea asociada
        Tarea tarea = tareaRepository.findById(idTarea).orElse(null);
        if (tarea == null) {
            throw new TareaServiceException("No existe la tarea con id " + idTarea);
        }

        // Crear la nueva subtarea y asociarla a la tarea
        Subtarea subtarea = new Subtarea(tarea, nombreSubtarea);
        tarea.addSubtarea(subtarea); // Método auxiliar en la entidad Tarea
        subtareaRepository.save(subtarea);

        return modelMapper.map(subtarea, SubtareaData.class);
    }

    @Transactional
    public void eliminarSubtarea(Long idSubtarea) {
        logger.debug("Eliminando subtarea con id " + idSubtarea);

        // Buscar la subtarea
        Subtarea subtarea = subtareaRepository.findById(idSubtarea).orElse(null);
        if (subtarea == null) {
            throw new TareaServiceException("No existe la subtarea con id " + idSubtarea);
        }

        // Eliminar la subtarea de la tarea y del repositorio
        Tarea tarea = subtarea.getTarea();
        tarea.removeSubtarea(subtarea); // Método auxiliar en la entidad Tarea
        subtareaRepository.delete(subtarea);
    }

    @Transactional(readOnly = true)
    public List<SubtareaData> allSubtareasTarea(Long idTarea) {
        logger.debug("Obteniendo todas las subtareas de la tarea " + idTarea);

        // Buscar la tarea en el repositorio
        Tarea tarea = tareaRepository.findById(idTarea).orElse(null);
        if (tarea == null) {
            throw new TareaServiceException("No existe la tarea con id " + idTarea);
        }

        // Convertir las subtareas de la tarea a DTOs y devolverlas
        return tarea.getSubtareas().stream()
                .map(subtarea -> modelMapper.map(subtarea, SubtareaData.class))
                .collect(Collectors.toList());
    }

    @Transactional(readOnly = true)
    public SubtareaData findSubtareaById(Long idSubtarea) {
        logger.debug("Buscando subtarea con id " + idSubtarea);

        // Buscar la subtarea en el repositorio
        Subtarea subtarea = subtareaRepository.findById(idSubtarea).orElse(null);
        if (subtarea == null) {
            throw new TareaServiceException("No existe la subtarea con id " + idSubtarea);
        }

        // Convertir la subtarea a DTO
        return modelMapper.map(subtarea, SubtareaData.class);
    }
    @Transactional
    public void asignarPrioridad(Long tareaId, String prioridad) {
        Tarea tarea = tareaRepository.findById(tareaId).orElseThrow(() ->
                new TareaServiceException("No existe tarea con id " + tareaId));
        tarea.setPrioridad(prioridad.toUpperCase());
        tareaRepository.save(tarea);
    }

    @Transactional(readOnly = true)
    public List<TareaData> allTareasUsuarioOrdenadasPorPrioridad(Long idUsuario, String orden) {
        Usuario usuario = usuarioRepository.findById(idUsuario).orElse(null);
        if (usuario == null) {
            throw new TareaServiceException("Usuario no encontrado");
        }

        List<Tarea> tareasOrdenadas;
        if ("DESC".equalsIgnoreCase(orden)) {
            tareasOrdenadas = tareaRepository.findAllByUsuarioOrderByPrioridadDesc(usuario.getId());
        } else {
            tareasOrdenadas = tareaRepository.findAllByUsuarioOrderByPrioridadAsc(usuario.getId());
        }

        return tareasOrdenadas.stream()
                .map(tarea -> modelMapper.map(tarea, TareaData.class))
                .collect(Collectors.toList());
    }



}
