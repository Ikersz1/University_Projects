package madstodolist.controller;

import madstodolist.authentication.ManagerUserSession;
import madstodolist.controller.exception.UsuarioNoLogeadoException;
import madstodolist.controller.exception.TareaNotFoundException;
import madstodolist.dto.EquipoData;
import madstodolist.dto.SubtareaData;
import madstodolist.dto.TareaData;
import madstodolist.dto.UsuarioData;
import madstodolist.service.EquipoService;
import madstodolist.service.TareaService;
import madstodolist.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import javax.servlet.http.HttpSession;
import java.util.ArrayList;
import java.util.List;

@Controller
public class TareaController {

    @Autowired
    UsuarioService usuarioService;

    @Autowired
    EquipoService equipoService;

    @Autowired
    TareaService tareaService;

    @Autowired
    ManagerUserSession managerUserSession;


    private void comprobarUsuarioLogeado(Long idUsuario) {
        Long idUsuarioLogeado = managerUserSession.usuarioLogeado();
        if (!idUsuario.equals(idUsuarioLogeado))
            throw new UsuarioNoLogeadoException();
    }

    @GetMapping("/usuarios/{id}/tareas/nueva")
    public String formNuevaTarea(@PathVariable(value="id") Long idUsuario,
                                 @ModelAttribute TareaData tareaData, Model model,
                                 HttpSession session) {

        comprobarUsuarioLogeado(idUsuario);

        UsuarioData usuario = usuarioService.findById(idUsuario);
        model.addAttribute("usuario", usuario);
        return "formNuevaTarea";
    }

    @PostMapping("/usuarios/{id}/tareas/nueva")
    public String nuevaTarea(@PathVariable(value="id") Long idUsuario, @ModelAttribute TareaData tareaData,
                             Model model, RedirectAttributes flash,
                             HttpSession session) {

        comprobarUsuarioLogeado(idUsuario);

        tareaService.nuevaTareaUsuario(idUsuario, tareaData.getTitulo());
        flash.addFlashAttribute("mensaje", "Tarea creada correctamente");
        return "redirect:/usuarios/" + idUsuario + "/tareas";
     }

    @GetMapping("/equipos/{id}/tareas/nueva")
    public String formNuevaTareaEquipo(@PathVariable(value = "id") Long idEquipo,
                                       @ModelAttribute TareaData tareaData, Model model,
                                       HttpSession session) {


        EquipoData equipo = equipoService.findById(idEquipo);
        model.addAttribute("equipo", equipo);
        return "formNuevaTareaEquipo";
    }

    @PostMapping("/equipos/{id}/tareas/nueva")
    public String nuevaTareaEquipo(@PathVariable(value = "id") Long idEquipo,
                                   @ModelAttribute TareaData tareaData,
                                   Model model, RedirectAttributes flash,
                                   HttpSession session) {

        tareaService.nuevaTareaEquipo(idEquipo, tareaData.getTitulo());
        flash.addFlashAttribute("mensaje", "Tarea creada correctamente para el equipo");
        return "redirect:/equipos/" + idEquipo;
    }


    @GetMapping("/usuarios/{id}/tareas")
    public String listadoTareas(@PathVariable(value = "id") Long idUsuario,
                                @RequestParam(value = "ordenarPorPrioridad", required = false, defaultValue = "false") boolean ordenarPorPrioridad,
                                @RequestParam(value = "orden", required = false, defaultValue = "ASC") String orden,
                                Model model, HttpSession session) {
        comprobarUsuarioLogeado(idUsuario);

        UsuarioData usuario = usuarioService.findById(idUsuario);
        List<TareaData> tareas;

        if (ordenarPorPrioridad) {
            tareas = tareaService.allTareasUsuarioOrdenadasPorPrioridad(idUsuario, orden);
        } else {
            tareas = tareaService.allTareasUsuario(idUsuario);
        }

        model.addAttribute("usuario", usuario);
        model.addAttribute("tareas", tareas);
        return "listaTareas";
    }


    @GetMapping("/tareas/{id}/editar")
    public String formEditaTarea(@PathVariable(value="id") Long idTarea, @ModelAttribute TareaData tareaData,
                                 Model model, HttpSession session) {

        TareaData tarea = tareaService.findById(idTarea);
        if (tarea == null) {
            throw new TareaNotFoundException();
        }

        model.addAttribute("tarea", tarea);
        tareaData.setTitulo(tarea.getTitulo());


        return "formEditarTarea";


    }

    @PostMapping("/tareas/{id}/editar")
    public String grabaTareaModificada(@PathVariable(value="id") Long idTarea, @ModelAttribute TareaData tareaData,
                                       Model model, RedirectAttributes flash, HttpSession session) {
        TareaData tarea = tareaService.findById(idTarea);
        if (tarea == null) {
            throw new TareaNotFoundException();
        }

        tareaService.modificaTarea(idTarea, tareaData.getTitulo());
        flash.addFlashAttribute("mensaje", "Tarea modificada correctamente");

        if(tareaService.findById(idTarea).getUsuarioId() != null) {
            return "redirect:/usuarios/" + tareaService.findById(idTarea).getUsuarioId() + "/tareas";
        }

        else{
            return "redirect:/equipos/" + tareaService.findById(idTarea).getEquipoId();
        }
    }

    @GetMapping("/tareas/{id}/editar/equipo")
    public String formEditaTareaEquipo(@PathVariable(value="id") Long idTarea, @ModelAttribute TareaData tareaData,
                                 Model model, HttpSession session) {

        TareaData tarea = tareaService.findById(idTarea);
        if (tarea == null) {
            throw new TareaNotFoundException();
        }

        EquipoData equipo = equipoService.findById(tarea.getEquipoId()); // Obtener el equipo
        if (equipo != null) {
            model.addAttribute("equipo", equipo);  // Agregar el equipo al modelo
        }

        model.addAttribute("tarea", tarea);
        tareaData.setTitulo(tarea.getTitulo());

        if (tareaService.findById(idTarea).getUsuarioId() != null) {
            return "formEditarTarea";
        } else {
            return "formEditarTareaEquipo";
        }
    }

    @PostMapping("/tareas/{id}/editar/equipo")
    public String grabaTareaModificadaEquipo(@PathVariable(value="id") Long idTarea, @ModelAttribute TareaData tareaData,
                                       Model model, RedirectAttributes flash, HttpSession session) {
        TareaData tarea = tareaService.findById(idTarea);
        if (tarea == null) {
            throw new TareaNotFoundException();
        }

        tareaService.modificaTarea(idTarea, tareaData.getTitulo());
        flash.addFlashAttribute("mensaje", "Tarea modificada correctamente");


        return "redirect:/equipos/" + tareaService.findById(idTarea).getEquipoId();

    }


    @DeleteMapping("/tareas/{id}")
    @ResponseBody
    // La anotación @ResponseBody sirve para que la cadena devuelta sea la resupuesta
    // de la petición HTTP, en lugar de una plantilla thymeleaf
    public String borrarTarea(@PathVariable(value="id") Long idTarea, RedirectAttributes flash, HttpSession session) {
        TareaData tarea = tareaService.findById(idTarea);
        if (tarea == null) {
            throw new TareaNotFoundException();
        }

        tareaService.borraTarea(idTarea);
        return "";
    }

    @PostMapping("/tareas/{id}/destacar")
    @ResponseBody
    public String destacarTarea(@PathVariable(value = "id") Long idTarea,
                                @RequestParam(value = "destacar") boolean destacar) {
        tareaService.destacarTarea(idTarea, destacar);
        return "ok";
    }

    @GetMapping("/tareas/{id}/subtareas/nueva")
    public String formNuevaSubtarea(@PathVariable(value = "id") Long idTarea,
                                    Model model, HttpSession session) {
        TareaData tarea = tareaService.findById(idTarea);
        if (tarea == null) {
            throw new TareaNotFoundException();
        }

        model.addAttribute("tarea", tarea);
        model.addAttribute("subtareaData", new SubtareaData());
        return "formNuevaSubtarea";
    }

    @PostMapping("/tareas/{id}/subtareas/nueva")
    public String nuevaSubtarea(@PathVariable(value = "id") Long idTarea,
                                @ModelAttribute SubtareaData subtareaData,
                                RedirectAttributes flash) {
        TareaData tarea = tareaService.findById(idTarea);
        if (tarea == null) {
            throw new TareaNotFoundException();
        }

       Long idUsuario = null;


        if (tarea.getUsuarioId() != null) {
            idUsuario = tarea.getUsuarioId();
            comprobarUsuarioLogeado(idUsuario);
        }

        tareaService.crearSubtarea(idTarea, subtareaData.getNombre());
        flash.addFlashAttribute("mensaje", "Subtarea creada correctamente");

        if(idUsuario != null) {
            return "redirect:/usuarios/" + idUsuario + "/tareas";
        }

        else{
           Long equipoID = tarea.getEquipoId();
           return "redirect:/equipos/" + equipoID;
        }
    }

    @DeleteMapping("/subtareas/{id}")
    @ResponseBody
    public String borrarSubtarea(@PathVariable(value = "id") Long idSubtarea, HttpSession session) {
        SubtareaData subtarea = tareaService.findSubtareaById(idSubtarea);
        if (subtarea == null) {
            throw new TareaNotFoundException();
        }

        tareaService.eliminarSubtarea(idSubtarea);
        return "";
    }
    @PostMapping("/tareas/{id}/prioridad")
    public String asignarPrioridad(@PathVariable Long id, @RequestParam int prioridad) {
        String prioridadTexto;
        switch (prioridad) {
            case 1:
                prioridadTexto = "ALTA";
                break;
            case 2:
                prioridadTexto = "MEDIA";
                break;
            case 3:
                prioridadTexto = "BAJA";
                break;
            default:
                throw new IllegalArgumentException("Prioridad no válida");
        }
        tareaService.asignarPrioridad(id, prioridadTexto);

        if(tareaService.findById(id).getUsuarioId() != null) {
            return "redirect:/usuarios/" + tareaService.findById(id).getUsuarioId() + "/tareas";
        }

        else{
            return "redirect:/equipos/" + tareaService.findById(id).getEquipoId();
        }
    }


}

