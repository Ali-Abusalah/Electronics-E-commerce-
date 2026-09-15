import { useState } from 'react'
import { Link, NavLink, useNavigate, useSearchParams } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { useCart } from '../context/CartContext'
import { useWishlist } from '../context/WishlistContext'
import { useDarkMode } from '../context/DarkModeContext'

const CATEGORIES = [
  { name: 'All', value: '' },
  { name: 'TVs', value: 'TVs' },
  { name: 'Smartphones', value: 'Smartphones' },
  { name: 'Laptops', value: 'Laptops' },
  { name: 'Audio', value: 'Audio' },
  { name: 'Wearables', value: 'Wearables' },
  { name: 'Accessories', value: 'Accessories' },
  { name: 'Gaming', value: 'Gaming' },
]

export default function Navbar({ onOpenCart }) {
  const { user, logout } = useAuth()
  const { cartCount } = useCart()
  const { wishlistCount } = useWishlist()
  const { dark, toggle: toggleDark } = useDarkMode()
  const navigate = useNavigate()
  const [searchParams] = useSearchParams()
  const [search, setSearch] = useState(searchParams.get('search') || '')

  const handleSearch = (e) => {
    e.preventDefault()
    const params = new URLSearchParams(searchParams)
    if (search.trim()) {
      params.set('search', search.trim())
    } else {
      params.delete('search')
    }
    navigate(`/?${params.toString()}`)
  }

  const handleCategory = (value) => {
    const params = new URLSearchParams(searchParams)
    if (value) {
      params.set('category', value)
    } else {
      params.delete('category')
    }
    navigate(`/?${params.toString()}`)
  }

  return (
    <header className="navbar">
      <div className="navbar-top">
        <Link to="/" className="brand">
          <img src="/logo.svg" alt="DCTech Shop" style={{ height: '40px' }} />
        </Link>

        <form className="search-bar" onSubmit={handleSearch} role="search">
          <input
            type="search"
            placeholder="Search products, brands..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            aria-label="Search products"
          />
          <button type="submit" className="search-btn" aria-label="Search">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
              <circle cx="11" cy="11" r="7" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </button>
        </form>

        <nav className="nav-actions" aria-label="Account">
          <button
            type="button"
            className="dark-toggle"
            onClick={toggleDark}
            aria-label={dark ? 'Switch to light mode' : 'Switch to dark mode'}
            data-tooltip={dark ? 'Light mode' : 'Dark mode'}
          >
            {dark ? '☀️' : '🌙'}
          </button>
          {user ? (
            <div className="user-menu">
              <span className="user-chip">Hi, {user.name}</span>
              <Link to="/orders" className="btn btn-ghost btn-sm">
                My Orders
              </Link>
              <button
                type="button"
                className="btn btn-ghost btn-sm"
                onClick={logout}
              >
                Logout
              </button>
            </div>
          ) : (
            <div className="auth-links">
              <Link to="/login" className="btn btn-ghost btn-sm">Login</Link>
              <Link to="/register" className="btn btn-primary btn-sm">Register</Link>
            </div>
          )}

          <button
            type="button"
            className="icon-btn"
            onClick={onOpenCart}
            aria-label={`Open cart, ${cartCount} items`}
            data-tooltip="Cart"
          >
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M6 6h15l-1.5 9h-12z" />
              <path d="M6 6L5 3H2" />
              <circle cx="9" cy="20" r="1.5" />
              <circle cx="17" cy="20" r="1.5" />
            </svg>
            {cartCount > 0 && <span className="icon-count">{cartCount}</span>}
          </button>

          <Link
            to="/wishlist"
            className="icon-btn"
            aria-label={`Wishlist, ${wishlistCount} items`}
            data-tooltip="Wishlist"
          >
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-.1a5.5 5.5 0 0 0-7.8 7.8l.1 1.7 1.7-.1a5.5 5.5 0 0 0 7.8 7.8l1 .1 1-.1a5.5 5.5 0 0 0 7.8-7.8l-.1-1.7z" />
            </svg>
            {wishlistCount > 0 && <span className="icon-count">{wishlistCount}</span>}
          </Link>
        </nav>
      </div>

      <nav className="nav-categories" aria-label="Categories">
        {CATEGORIES.map((cat) => (
          <NavLink
            key={cat.value || 'all'}
            to={`/?${cat.value ? `category=${encodeURIComponent(cat.value)}` : ''}`}
            className={({ isActive }) =>
              `category-link ${isActive && !cat.value ? 'active' : ''}`
            }
            onClick={() => handleCategory(cat.value)}
          >
            {cat.name}
          </NavLink>
        ))}
      </nav>
    </header>
  )
}
