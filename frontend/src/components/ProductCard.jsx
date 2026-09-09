import { useCart } from '../context/CartContext'
import { useWishlist } from '../context/WishlistContext'
import { formatPrice } from '../utils/format'

export default function ProductCard({ product, onQuickView }) {
  const { addToCart } = useCart()
  const { toggle, isWishlisted } = useWishlist()
  const inStock = product.stock > 0

  return (
    <article className="product-card">
      <div className="product-card-media">
        <button
          type="button"
          className="product-image-btn"
          onClick={() => onQuickView(product)}
          aria-label={`Quick view ${product.name}`}
        >
          <img
            src={product.image}
            alt={product.alt || product.name}
            loading="lazy"
            onError={(e) => {
              e.currentTarget.src =
                'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=600&h=450&fit=crop&q=80&auto=format'
            }}
          />
        </button>
        <span className={`stock-badge ${inStock ? 'in-stock' : 'out-of-stock'}`}>
          {inStock ? `${product.stock} in stock` : 'Out of stock'}
        </span>
        <button
          type="button"
          className={`wishlist-btn ${isWishlisted(product.id) ? 'active' : ''}`}
          onClick={() => toggle(product)}
          aria-label={isWishlisted(product.id) ? 'Remove from wishlist' : 'Add to wishlist'}
        >
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-.1a5.5 5.5 0 0 0-7.8 7.8l.1 1.7 1.7-.1a5.5 5.5 0 0 0 7.8 7.8l1 .1 1-.1a5.5 5.5 0 0 0 7.8-7.8l-.1-1.7z" />
          </svg>
        </button>
        <button
          type="button"
          className="quick-view-btn"
          onClick={() => onQuickView(product)}
        >
          Quick View
        </button>
      </div>

      <div className="product-card-body">
        <div className="product-card-meta">
          <span className="product-category">{product.category}</span>
          <span className="product-brand">{product.brand}</span>
        </div>
        <h3 className="product-name">{product.name}</h3>
        <div className="product-card-footer">
          <div className="product-price">{formatPrice(product.price)}</div>
          <div className="product-rating" aria-label={`Rated ${product.rating} out of 5`}>
            <span aria-hidden="true">★</span> {product.rating.toFixed(1)}
            <span className="product-reviews">({product.reviews})</span>
          </div>
        </div>
        <button
          type="button"
          className="btn btn-primary btn-block"
          disabled={!inStock}
          onClick={() => addToCart(product)}
        >
          {inStock ? 'Add to Cart' : 'Unavailable'}
        </button>
      </div>
    </article>
  )
}
