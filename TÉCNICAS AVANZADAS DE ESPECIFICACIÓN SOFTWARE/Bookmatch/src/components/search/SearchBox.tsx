"use client"

import { useState } from 'react'
import { useRouter } from 'next/navigation'

interface SearchBoxProps {
  className?: string
  placeholder?: string
  onSearch?: (term: string) => void
}

export default function SearchBox({ 
  className = '', 
  placeholder = 'Buscar por título...', 
  onSearch 
}: SearchBoxProps) {
  const [searchTerm, setSearchTerm] = useState('')
  const router = useRouter()

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    if (searchTerm.trim()) {
      if (onSearch) {
        onSearch(searchTerm.trim())
      } else {
        router.push(`/search?q=${encodeURIComponent(searchTerm)}`)
      }
    }
  }

  return (
    <form onSubmit={handleSearch} className={`relative ${className}`}>
      <input
        type="text"
        value={searchTerm}
        onChange={(e) => setSearchTerm(e.target.value)}
        placeholder={placeholder}
        className="w-full py-2 pl-4 pr-10 text-sm bg-zinc-100 border border-zinc-200 rounded-full focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent"
      />
      <button
        type="submit"
        className="absolute right-0 top-0 mt-2 mr-3 text-zinc-500 hover:text-zinc-800"
        aria-label="Buscar"
      >
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          className="h-5 w-5" 
          fill="none" 
          viewBox="0 0 24 24" 
          stroke="currentColor"
        >
          <path 
            strokeLinecap="round" 
            strokeLinejoin="round" 
            strokeWidth={2} 
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
          />
        </svg>
      </button>
    </form>
  )
}