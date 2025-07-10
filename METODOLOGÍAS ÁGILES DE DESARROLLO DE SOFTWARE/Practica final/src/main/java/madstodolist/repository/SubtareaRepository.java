package madstodolist.repository;

import madstodolist.model.Subtarea;
import org.springframework.data.jpa.repository.JpaRepository;

public interface SubtareaRepository extends JpaRepository<Subtarea, Long> {
}
