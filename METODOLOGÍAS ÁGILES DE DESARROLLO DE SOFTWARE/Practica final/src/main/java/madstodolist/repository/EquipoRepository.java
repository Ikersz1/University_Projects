package madstodolist.repository;

import madstodolist.model.Equipo;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface EquipoRepository extends JpaRepository<Equipo, Long> {
    public List<Equipo> findAll();
    List<Equipo> findAllByOrderByNombreAsc();
    Optional<Equipo> findByNombre(String nombre);
}


