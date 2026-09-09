import { useState } from 'react'
import { Route, Routes } from 'react-router-dom'
import Navbar from './components/Navbar'
import CartDrawer from './components/CartDrawer'
import Shop from './pages/Shop'
import Login from './pages/Login'
import Register from './pages/Register'
import Checkout from './pages/Checkout'
import MyOrders from './pages/MyOrders'
import Wishlist from './pages/Wishlist'
import { WishlistProvider } from './context/WishlistContext'

export default function App() {
  const [cartOpen, setCartOpen] = useState(false)

  return (
    <WishlistProvider>
      <Navbar onOpenCart={() => setCartOpen(true)} />
      <CartDrawer open={cartOpen} onClose={() => setCartOpen(false)} />

      <main className="app-main">
        <Routes>
          <Route path="/" element={<Shop />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/checkout" element={<Checkout />} />
          <Route path="/orders" element={<MyOrders />} />
          <Route path="/wishlist" element={<Wishlist />} />
        </Routes>
      </main>

      <footer className="app-footer">
        <div className="footer-grid">
          <div className="footer-col footer-brand">
            <div className="footer-logo">
              <img src="/logo.svg" alt="DCTech Shop" style={{ height: '40px', filter: 'brightness(0) invert(1)' }} />
            </div>
            <p className="footer-tagline">Digital Creativity Tech Shop</p>
            <p className="footer-desc">
              Your one-stop shop for the latest electronics
              at the best prices. Quality gear, honest prices,
              fast delivery.
            </p>
            <div className="footer-social">
              <a href="#" aria-label="Facebook" className="social-icon">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                </svg>
              </a>
              <a href="#" aria-label="X (Twitter)" className="social-icon">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                  <path d="M4 20l6.768 -6.768m2.46 -2.46L20 4" />
                </svg>
              </a>
              <a href="#" aria-label="Instagram" className="social-icon">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                  <rect x="2" y="2" width="20" height="20" rx="5" />
                  <circle cx="12" cy="12" r="5" />
                  <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none" />
                </svg>
              </a>
              <a href="#" aria-label="YouTube" className="social-icon">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.6.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.4 19.6C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" />
                  <path d="M9.75 15.02l5.25-3.15v6.3z" />
                </svg>
              </a>
            </div>
          </div>

          <div className="footer-col">
            <h4 className="footer-heading">Shop</h4>
            <ul className="footer-links">
              <li><a href="/?category=TVs">TVs</a></li>
              <li><a href="/?category=Smartphones">Smartphones</a></li>
              <li><a href="/?category=Laptops">Laptops</a></li>
              <li><a href="/?category=Audio">Audio</a></li>
              <li><a href="/?category=Accessories">Accessories</a></li>
              <li><a href="/?category=Gaming">Gaming</a></li>
            </ul>
          </div>

          <div className="footer-col">
            <h4 className="footer-heading">Company</h4>
            <ul className="footer-links">
              <li><a href="#">About us</a></li>
              <li><a href="#">Careers</a></li>
              <li><a href="#">Press</a></li>
              <li><a href="#">Returns</a></li>
              <li><a href="#">Privacy policy</a></li>
            </ul>
          </div>

          <div className="footer-col">
            <h4 className="footer-heading">Contact</h4>
            <ul className="footer-links">
              <li>Amman, Jordan</li>
              <li>+962 7 9880 9635</li>
              <li>alissalah@gmail.com</li>
            </ul>
          </div>
        </div>

        <div className="footer-bottom">
          <div className="footer-perks">
            <span className="perk">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2">
                <rect x="1" y="3" width="15" height="13" rx="2" />
                <path d="M16 8h4l3 3v5h-7V8z" />
                <circle cx="5.5" cy="18.5" r="2.5" />
                <circle cx="18.5" cy="18.5" r="2.5" />
              </svg>
              Free shipping over $50
            </span>
            <span className="perk">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
              2-year warranty
            </span>
            <span className="perk">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
              </svg>
              24/7 support
            </span>
          </div>
          <p className="footer-copy">© 2026 DCTech Shop.</p>
        </div>
      </footer>
    </WishlistProvider>
  )
}
