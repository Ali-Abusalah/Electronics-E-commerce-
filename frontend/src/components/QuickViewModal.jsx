import { useEffect } from 'react'
import { useCart } from '../context/CartContext'
import { formatPrice } from '../utils/format'
import Reviews from './Reviews'

export default function QuickViewModal({ product, onClose }) {
  const { addToCart } = useCart()
  const inStock = product.stock > 0

  useEffect(() => {
    const onKey = (e) => {
      if (e.key === 'Escape') onClose()
    }
    document.addEventListener('keydown', onKey)
    document.body.style.overflow = 'hidden'
    return () => {
      document.removeEventListener('keydown', onKey)
      document.body.style.overflow = ''
    }
  }, [onClose])

  return (
    <div className="modal-overlay" onClick={onClose} role="presentation">
      <div
        className="modal"
        role="dialog"
        aria-modal="true"
        aria-label={product.name}
        onClick={(e) => e.stopPropagation()}
      >
        <button type="button" className="modal-close" onClick={onClose} aria-label="Close">
          ✕
        </button>
        <div className="modal-grid">
          <div className="modal-media">
            <img
              src={product.image}
              alt={product.alt || product.name}
              onError={(e) => {
                e.currentTarget.src =
                  'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=600&h=450&fit=crop&q=80&auto=format'
              }}
            />
            <span className={`stock-badge ${inStock ? 'in-stock' : 'out-of-stock'}`}>
              {inStock ? `${product.stock} in stock` : 'Out of stock'}
            </span>
          </div>

          <div className="modal-content">
            <div className="product-card-meta">
              <span className="product-category">{product.category}</span>
              <span className="product-brand">{product.brand}</span>
            </div>
            <h2 className="modal-title">{product.name}</h2>
            <div className="product-rating large" aria-label={`Rated ${product.rating} out of 5`}>
              <span aria-hidden="true">★</span> {product.rating.toFixed(1)}
              <span className="product-reviews">({product.reviews} reviews)</span>
            </div>
            <p className="modal-description">{product.description}</p>

            <ul className="feature-list">
              {product.features.map((feature) => (
                <li key={feature}>{feature}</li>
              ))}
            </ul>

            <div className="modal-price">{formatPrice(product.price)}</div>
            <button
              type="button"
              className="btn btn-primary btn-block"
              disabled={!inStock}
              onClick={() => {
                addToCart(product)
                onClose()
              }}
            >
              {inStock ? 'Add to Cart' : 'Unavailable'}
            </button>

            <Reviews productId={product.id} />
          </div>
        </div>
      </div>
    </div>
  )
}