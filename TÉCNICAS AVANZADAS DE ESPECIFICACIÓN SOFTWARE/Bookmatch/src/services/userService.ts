import { db, auth } from "../firebaseConfig";
import { doc, getDoc } from "firebase/firestore";

interface UserInfo {
  name: string;
  email: string;
}

// Function to get a user's information from their ID
export async function getUserInfo(userId: string): Promise<UserInfo> {
  try {
    // First check if this is the current user
    if (auth.currentUser?.uid === userId) {
      return {
        name: auth.currentUser.displayName || "Usuario actual",
        email: auth.currentUser.email || ""
      };
    }
    
    // Try to get the user from Firestore
    const userRef = doc(db, "users", userId);
    const userSnap = await getDoc(userRef);
    
    if (userSnap.exists()) {
      const userData = userSnap.data();
      return {
        name: userData.nombre || userData.name || "Usuario",
        email: userData.email || ""
      };
    }
    
    // If we can't find the user, return default values
    return {
      name: "Usuario",
      email: ""
    };
  } catch (error) {
    console.error("Error fetching user information:", error);
    return {
      name: "Usuario",
      email: ""
    };
  }
}

// For backward compatibility
export async function getUserDisplayName(userId: string): Promise<string> {
  const userInfo = await getUserInfo(userId);
  return userInfo.name;
}