package madstodolist.repository;

import madstodolist.model.Tarea;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;
import org.springframework.data.repository.query.Param;

import java.util.List;

public interface TareaRepository extends CrudRepository<Tarea, Long> {
    @Modifying
    @Query("UPDATE Tarea t SET t.destacado = :destacado WHERE t.id = :id")
    void actualizarDestacado(@Param("id") Long id, @Param("destacado") boolean destacado);

    @Query("SELECT t FROM Tarea t WHERE t.usuario.id = :usuarioId ORDER BY " +
            "CASE t.prioridad " +
            "WHEN 'ALTA' THEN 1 " +
            "WHEN 'MEDIA' THEN 2 " +
            "WHEN 'BAJA' THEN 3 END ASC")
    List<Tarea> findAllByUsuarioOrderByPrioridad(@Param("usuarioId") Long usuarioId);

    @Query("SELECT t FROM Tarea t WHERE t.usuario.id = :usuarioId ORDER BY " +
            "CASE t.prioridad " +
            "WHEN 'ALTA' THEN 1 " +
            "WHEN 'MEDIA' THEN 2 " +
            "WHEN 'BAJA' THEN 3 END ASC")
    List<Tarea> findAllByUsuarioOrderByPrioridadAsc(@Param("usuarioId") Long usuarioId);

    @Query("SELECT t FROM Tarea t WHERE t.usuario.id = :usuarioId ORDER BY " +
            "CASE t.prioridad " +
            "WHEN 'ALTA' THEN 1 " +
            "WHEN 'MEDIA' THEN 2 " +
            "WHEN 'BAJA' THEN 3 END DESC")
    List<Tarea> findAllByUsuarioOrderByPrioridadDesc(@Param("usuarioId") Long usuarioId);



}
