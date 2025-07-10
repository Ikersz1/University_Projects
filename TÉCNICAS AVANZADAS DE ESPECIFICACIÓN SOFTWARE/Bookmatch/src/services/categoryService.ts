import { db } from "../firebaseConfig";
import { doc, getDoc } from "firebase/firestore";

export async function getCategoryName(categoryId: string): Promise<string> {
  try {
    if (!categoryId || categoryId.trim() === '') {
      return "Sin categoría";
    }
    
    const categoryRef = doc(db, "categories", categoryId);
    const categorySnap = await getDoc(categoryRef);
    
    if (categorySnap.exists()) {
      const data = categorySnap.data();
      return data.nombre || "Categoría sin nombre";
    }
    
    return "Categoría desconocida";
  } catch (error) {
    console.error("Error fetching category:", error);
    return "Error de categoría";
  }
}