import { useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useCart } from '../context/CartContext'
import { formatPrice } from '../utils/format'

export default function CartDrawer({ open, onClose }) {
  const { items, removeFromCart, updateQuantity, cartCount, cartTotal } = useCart()
  const navigate = useNavigate()

  useEffect(() => {
    if (!open) return
    const onKey = (e) => {
      if (e.key === 'Escape') onClose()
    }
    document.addEventListener('keydown', onKey)
    document.body.style.overflow = 'hidden'
    return () => {
      document.removeEventListener('keydown', onKey)
      document.body.style.overflow = ''
    }
  }, [open, onClose])

  const handleCheckout = () => {
    onClose()
    navigate('/checkout')
  }

  return (
    <>
      <div
        className={`drawer-overlay ${open ? 'open' : ''}`}
        onClick={onClose}
        aria-hidden={!open}
      />
      <aside
        className={`cart-drawer ${open ? 'open' : ''}`}
        aria-hidden={!open}
        aria-label="Shopping cart"
      >
        <div className="drawer-header">
          <h2>Your Cart <span className="drawer-count">({cartCount})</span></h2>
          <button type="button" className="modal-close" onClick={onClose} aria-label="Close cart">
            ✕
          </button>
        </div>

        {items.length === 0 ? (
          <div className="cart-empty">
            <div className="empty-icon" aria-hidden="true">🛒</div>
            <p>Your cart is empty.</p>
            <Link to="/" className="btn btn-primary" onClick={onClose}>
              Start Shopping
            </Link>
          </div>
        ) : (
          <>
            <div className="drawer-items">
              {items.map((item) => (
                <div key={item.product.id} className="drawer-item">
                  <img
                    src={item.product.image}
                    alt={item.product.alt || item.product.name}
                    onError={(e) => {
                      e.currentTarget.src =
                        'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=200&h=150&fit=crop&q=80&auto=format'
                    }}
                  />
                  <div className="drawer-item-info">
                    <h3>{item.product.name}</h3>
                    <span className="drawer-item-price">
                      {formatPrice(item.product.price)}
                    </span>
                    <div className="qty-control">
                      <button
                        type="button"
                        aria-label="Decrease quantity"
                        onClick={() => updateQuantity(item.product.id, item.quantity - 1)}
                      >
                        −
                      </button>
                      <span>{item.quantity}</span>
                      <button
                        type="button"
                        aria-label="Increase quantity"
                        onClick={() => updateQuantity(item.product.id, item.quantity + 1)}
                      >
                        +
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    className="drawer-remove"
                    onClick={() => removeFromCart(item.product.id)}
                    aria-label={`Remove ${item.product.name}`}
                  >
                    ✕
                  </button>
                </div>
              ))}
            </div>

            <div className="drawer-footer">
              <div className="drawer-subtotal">
                <span>Subtotal</span>
                <strong>{formatPrice(cartTotal())}</strong>
              </div>
              <button type="button" className="btn btn-primary btn-block" onClick={handleCheckout}>
                Proceed to Checkout
              </button>
            </div>
          </>
        )}
      </aside>
    </>
  )
}