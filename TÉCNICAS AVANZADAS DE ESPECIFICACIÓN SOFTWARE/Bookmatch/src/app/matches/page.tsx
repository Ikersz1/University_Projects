"use client"

import { useEffect, useState, useCallback } from "react"
import { db, auth } from "../../firebaseConfig"
import { collection, query, where, getDocs, doc, getDoc, updateDoc } from "firebase/firestore"
import { onAuthStateChanged } from "firebase/auth"
import { useRouter } from "next/navigation"
import Image from "next/image"
import Link from "next/link"

interface BookExchange {
  id: string
  fecha: string
  hora: string
  libroSolicitado: string
  libroOfrecido: string
  usuarioSolicitante: string
  usuarioReceptor: string
  rechazado: boolean
  aceptado: boolean
  createdAt: Date
  // Datos adicionales que cargaremos
  libroSolicitadoData?: {
    titulo: string
    imageUrl?: string
  }
  libroOfrecidoData?: {
    titulo: string
    imageUrl?: string
  }
  usuarioSolicitanteData?: {
    nombre: string
  }
  usuarioReceptorData?: {
    nombre: string
  }
}

export default function MisIntercambios() {
  const [intercambiosRecibidos, setIntercambiosRecibidos] = useState<BookExchange[]>([])
  const [intercambiosEnviados, setIntercambiosEnviados] = useState<BookExchange[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const router = useRouter()

  // Función para enriquecer los datos del intercambio con información de libros y usuarios
  const enrichExchangeData = async (exchange: BookExchange): Promise<BookExchange> => {
    const enrichedExchange = { ...exchange }

    try {
      // Cargar datos del libro solicitado
      const libroSolicitadoDoc = await getDoc(doc(db, "books", exchange.libroSolicitado))
      if (libroSolicitadoDoc.exists()) {
        enrichedExchange.libroSolicitadoData = {
          titulo: libroSolicitadoDoc.data().titulo || "Libro sin título",
          imageUrl: libroSolicitadoDoc.data().imageUrl,
        }
      }

      // Cargar datos del libro ofrecido
      const libroOfrecidoDoc = await getDoc(doc(db, "books", exchange.libroOfrecido))
      if (libroOfrecidoDoc.exists()) {
        enrichedExchange.libroOfrecidoData = {
          titulo: libroOfrecidoDoc.data().titulo || "Libro sin título",
          imageUrl: libroOfrecidoDoc.data().imageUrl,
        }
      }

      // Cargar datos del usuario solicitante
      const usuarioSolicitanteDoc = await getDoc(doc(db, "users", exchange.usuarioSolicitante))
      if (usuarioSolicitanteDoc.exists()) {
        enrichedExchange.usuarioSolicitanteData = {
          nombre: usuarioSolicitanteDoc.data().nombre || "Usuario desconocido",
        }
      }

      // Cargar datos del usuario receptor
      const usuarioReceptorDoc = await getDoc(doc(db, "users", exchange.usuarioReceptor))
      if (usuarioReceptorDoc.exists()) {
        enrichedExchange.usuarioReceptorData = {
          nombre: usuarioReceptorDoc.data().nombre || "Usuario desconocido",
        }
      }
    } catch (err) {
      console.error("Error al enriquecer datos del intercambio:", err)
    }

    return enrichedExchange
  }


  const loadExchanges = useCallback(async (userId: string) => {
    // Cargar intercambios recibidos (donde el usuario es el receptor)
    const recibidosQuery = query(
      collection(db, "requests"),
      where("usuarioReceptor", "==", userId),
      // Eliminar orderBy si causa problemas
      // orderBy("createdAt", "desc")
    )

    // Cargar intercambios enviados (donde el usuario es el solicitante)
    const enviadosQuery = query(
      collection(db, "requests"),
      where("usuarioSolicitante", "==", userId),
      // Eliminar orderBy si causa problemas
      // orderBy("createdAt", "desc")
    )

    const [recibidosSnapshot, enviadosSnapshot] = await Promise.all([getDocs(recibidosQuery), getDocs(enviadosQuery)])

    const recibidosPromises = recibidosSnapshot.docs.map(async (docSnapshot) => {
      const data = docSnapshot.data() as BookExchange
      return await enrichExchangeData({
        ...data,
        id: docSnapshot.id,
        createdAt: data.createdAt || new Date(),
      })
    })

    const enviadosPromises = enviadosSnapshot.docs.map(async (docSnapshot) => {
      const data = docSnapshot.data() as BookExchange
      return await enrichExchangeData({
        ...data,
        id: docSnapshot.id,
        createdAt: data.createdAt || new Date(),
      })
    })

    const [recibidosData, enviadosData] = await Promise.all([
      Promise.all(recibidosPromises),
      Promise.all(enviadosPromises),
    ])

    setIntercambiosRecibidos(recibidosData)
    setIntercambiosEnviados(enviadosData)
  }, [])
  
  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, async (user) => {
      if (!user) {
        router.push("/login")
        return
      }
  
      try {
        await loadExchanges(user.uid)
      } catch (err) {
        console.error("Error al cargar intercambios:", err)
        setError("Error al cargar los intercambios")
      } finally {
        setLoading(false)
      }
    })
  
    return () => unsubscribe()
  }, [router, loadExchanges])

  

  

  // Función para aceptar o rechazar un intercambio
  const handleExchangeAction = async (exchangeId: string, accept: boolean) => {
    try {
      if (accept) {
        // Aquí podrías implementar la lógica para aceptar el intercambio
        // Por ejemplo, cambiar el estado del intercambio a "aceptado"
        await updateDoc(doc(db, "requests", exchangeId), {
          aceptado: true,
        })
      } else {
        // Marcar como rechazado
        await updateDoc(doc(db, "requests", exchangeId), {
          rechazado: true,
        })
      }

      // Recargar los intercambios para reflejar los cambios
      if (auth.currentUser) {
        await loadExchanges(auth.currentUser.uid)
      }
    } catch (err) {
      console.error("Error al procesar la acción:", err)
      setError("Error al procesar la acción")
    }
  }

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-[400px]">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-zinc-900"></div>
      </div>
    )
  }

  return (
    <div className="max-w-4xl mx-auto p-4">
      <h1 className="text-3xl font-bold mb-8 text-center">Mis Intercambios</h1>

      {error && (
        <div className="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
          <p className="text-red-700">{error}</p>
        </div>
      )}

      <div className="mb-10">
        <h2 className="text-xl font-semibold mb-4 border-b pb-2">Solicitudes Recibidas</h2>
        {intercambiosRecibidos.length > 0 ? (
          <div className="space-y-4">
            {intercambiosRecibidos.map((intercambio) => (
              <div
                key={intercambio.id}
                className={`bg-white rounded-lg shadow-md p-4 ${intercambio.rechazado ? "opacity-70" : ""}`}
              >
                <div className="flex flex-col md:flex-row gap-4">
                  <div className="flex-1">
                    <p className="text-sm text-zinc-500 mb-1">
                      {intercambio.fecha} a las {intercambio.hora}
                    </p>
                    <p className="font-medium mb-2">
                      <span className="text-zinc-600">De:</span>{" "}
                      {intercambio.usuarioSolicitanteData?.nombre || "Usuario desconocido"}
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                      <div className="border rounded-lg p-3">
                        <p className="text-sm font-medium text-zinc-500">Quiere tu libro:</p>
                        <div className="flex items-center mt-2">
                          {intercambio.libroSolicitadoData?.imageUrl ? (
                            <Image
                              src={intercambio.libroSolicitadoData.imageUrl || "/placeholder.svg"}
                              alt={intercambio.libroSolicitadoData.titulo}
                              width={50}
                              height={70}
                              className="object-cover rounded"
                            />
                          ) : (
                            <div className="w-[50px] h-[70px] bg-zinc-200 rounded flex items-center justify-center">
                              <span className="text-zinc-400 text-xs">Sin imagen</span>
                            </div>
                          )}
                          <div className="ml-3">
                            <p className="font-medium">{intercambio.libroSolicitadoData?.titulo}</p>
                            <Link
                              href={`/books/${intercambio.libroSolicitado}`}
                              className="text-sm text-blue-600 hover:underline"
                            >
                              Ver detalles
                            </Link>
                          </div>
                        </div>
                      </div>

                      <div className="border rounded-lg p-3">
                        <p className="text-sm font-medium text-zinc-500">A cambio de su libro:</p>
                        <div className="flex items-center mt-2">
                          {intercambio.libroOfrecidoData?.imageUrl ? (
                            <Image
                              src={intercambio.libroOfrecidoData.imageUrl || "/placeholder.svg"}
                              alt={intercambio.libroOfrecidoData.titulo}
                              width={50}
                              height={70}
                              className="object-cover rounded"
                            />
                          ) : (
                            <div className="w-[50px] h-[70px] bg-zinc-200 rounded flex items-center justify-center">
                              <span className="text-zinc-400 text-xs">Sin imagen</span>
                            </div>
                          )}
                          <div className="ml-3">
                            <p className="font-medium">{intercambio.libroOfrecidoData?.titulo}</p>
                            <Link
                              href={`/books/${intercambio.libroOfrecido}`}
                              className="text-sm text-blue-600 hover:underline"
                            >
                              Ver detalles
                            </Link>
                          </div>
                        </div>
                      </div>
                    </div>

                    {intercambio.rechazado ? (
                      <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">
                        Has rechazado esta solicitud de intercambio.
                      </div>
                    ) : intercambio.aceptado ? (
                      <div className="bg-green-50 border-l-4 border-green-500 p-3 text-sm text-green-700">
                        Has aceptado esta solicitud de intercambio.
                      </div>
                    ) : (
                      <div className="flex gap-2">
                        <button
                          onClick={() => handleExchangeAction(intercambio.id, true)}
                          className="btn-sm bg-green-600 hover:bg-green-700 text-white flex-1"
                        >
                          Aceptar Intercambio
                        </button>
                        <button
                          onClick={() => handleExchangeAction(intercambio.id, false)}
                          className="btn-sm bg-red-600 hover:bg-red-700 text-white flex-1"
                        >
                          Rechazar
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <p className="text-zinc-500 text-center py-6">No has recibido solicitudes de intercambio.</p>
        )}
      </div>

      <div>
        <h2 className="text-xl font-semibold mb-4 border-b pb-2">Solicitudes Enviadas</h2>
        {intercambiosEnviados.length > 0 ? (
          <div className="space-y-4">
            {intercambiosEnviados.map((intercambio) => (
              <div
                key={intercambio.id}
                className={`bg-white rounded-lg shadow-md p-4 ${intercambio.rechazado ? "opacity-70" : ""}`}
              >
                <div className="flex flex-col md:flex-row gap-4">
                  <div className="flex-1">
                    <p className="text-sm text-zinc-500 mb-1">
                      {intercambio.fecha} a las {intercambio.hora}
                    </p>
                    <p className="font-medium mb-2">
                      <span className="text-zinc-600">Para:</span>{" "}
                      {intercambio.usuarioReceptorData?.nombre || "Usuario desconocido"}
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                      <div className="border rounded-lg p-3">
                        <p className="text-sm font-medium text-zinc-500">Quieres su libro:</p>
                        <div className="flex items-center mt-2">
                          {intercambio.libroSolicitadoData?.imageUrl ? (
                            <Image
                              src={intercambio.libroSolicitadoData.imageUrl || "/placeholder.svg"}
                              alt={intercambio.libroSolicitadoData.titulo}
                              width={50}
                              height={70}
                              className="object-cover rounded"
                            />
                          ) : (
                            <div className="w-[50px] h-[70px] bg-zinc-200 rounded flex items-center justify-center">
                              <span className="text-zinc-400 text-xs">Sin imagen</span>
                            </div>
                          )}
                          <div className="ml-3">
                            <p className="font-medium">{intercambio.libroSolicitadoData?.titulo}</p>
                            <Link
                              href={`/books/${intercambio.libroSolicitado}`}
                              className="text-sm text-blue-600 hover:underline"
                            >
                              Ver detalles
                            </Link>
                          </div>
                        </div>
                      </div>

                      <div className="border rounded-lg p-3">
                        <p className="text-sm font-medium text-zinc-500">A cambio de tu libro:</p>
                        <div className="flex items-center mt-2">
                          {intercambio.libroOfrecidoData?.imageUrl ? (
                            <Image
                              src={intercambio.libroOfrecidoData.imageUrl || "/placeholder.svg"}
                              alt={intercambio.libroOfrecidoData.titulo}
                              width={50}
                              height={70}
                              className="object-cover rounded"
                            />
                          ) : (
                            <div className="w-[50px] h-[70px] bg-zinc-200 rounded flex items-center justify-center">
                              <span className="text-zinc-400 text-xs">Sin imagen</span>
                            </div>
                          )}
                          <div className="ml-3">
                            <p className="font-medium">{intercambio.libroOfrecidoData?.titulo}</p>
                            <Link
                              href={`/books/${intercambio.libroOfrecido}`}
                              className="text-sm text-blue-600 hover:underline"
                            >
                              Ver detalles
                            </Link>
                          </div>
                        </div>
                      </div>
                    </div>

                    {intercambio.rechazado ? (
                      <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">
                        Tu solicitud de intercambio ha sido rechazada.
                      </div>
                    ) : intercambio.aceptado ? (
                      <div className="bg-green-50 border-l-4 border-green-500 p-3 text-sm text-green-700">
                        Tu solicitud de intercambio ha sido aceptada.
                      </div>
                    ) : (
                      <div className="bg-yellow-50 border-l-4 border-yellow-500 p-3 text-sm text-yellow-700">
                        Solicitud pendiente de respuesta.
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <p className="text-zinc-500 text-center py-6">No has enviado solicitudes de intercambio.</p>
        )}
      </div>
    </div>
  )
}
