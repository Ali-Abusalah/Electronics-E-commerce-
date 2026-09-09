import { Link } from 'react-router-dom'
import { useWishlist } from '../context/WishlistContext'
import { useCart } from '../context/CartContext'

export default function Wishlist() {
  const { items, toggle } = useWishlist()
  const { addToCart } = useCart()

  return (
    <div className="orders-page">
      <h1 className="orders-title">My Wishlist</h1>
      <p className="orders-subtitle">{items.length} {items.length === 1 ? 'item' : 'items'} saved</p>

      {items.length === 0 ? (
        <div className="orders-empty">
          <span className="empty-icon">
            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" strokeWidth="1.5">
              <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-.1a5.5 5.5 0 0 0-7.8 7.8l.1 1.7 1.7-.1a5.5 5.5 0 0 0 7.8 7.8l1 .1 1-.1a5.5 5.5 0 0 0 7.8-7.8l-.1-1.7z" />
            </svg>
          </span>
          <h3>Your wishlist is empty</h3>
          <p>Browse products and tap the heart icon to save your favorites.</p>
          <Link to="/" className="btn btn-primary">Browse Products</Link>
        </div>
      ) : (
        <div className="product-grid">
          {items.map((product) => (
            <article key={product.id} className="product-card">
              <div className="product-card-media">
                <img
                  src={product.image}
                  alt={product.alt || product.name}
                  loading="lazy"
                  onError={(e) => {
                    e.currentTarget.src =
                      'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=600&h=450&fit=crop&q=80&auto=format'
                  }}
                />
                <button
                  type="button"
                  className="wishlist-btn active"
                  onClick={() => toggle(product)}
                  aria-label="Remove from wishlist"
                >
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-.1a5.5 5.5 0 0 0-7.8 7.8l.1 1.7 1.7-.1a5.5 5.5 0 0 0 7.8 7.8l1 .1 1-.1a5.5 5.5 0 0 0 7.8-7.8l-.1-1.7z" />
                  </svg>
                </button>
              </div>
              <div className="product-card-body">
                <div className="product-card-meta">
                  <span className="product-category">{product.category}</span>
                  <span className="product-brand">{product.brand}</span>
                </div>
                <h3 className="product-name">{product.name}</h3>
                <div className="product-card-footer">
                  <div className="product-price">${product.price.toFixed(2)}</div>
                  <div className="product-rating">
                    <span>★</span> {product.rating.toFixed(1)}
                  </div>
                </div>
                <button
                  type="button"
                  className="btn btn-primary btn-block"
                  disabled={product.stock <= 0}
                  onClick={() => addToCart(product)}
                >
                  {product.stock > 0 ? 'Add to Cart' : 'Unavailable'}
                </button>
              </div>
            </article>
          ))}
        </div>
      )}
    </div>
  )
}
