import ProductCard from './ProductCard'

export default function ProductGrid({ products, onQuickView }) {
  if (products.length === 0) {
    return (
      <div className="empty-state">
        <div className="empty-icon" aria-hidden="true">🔍</div>
        <h3>No products found</h3>
        <p>Try adjusting your search or filter criteria.</p>
      </div>
    )
  }

  return (
    <div className="product-grid">
      {products.map((product) => (
        <ProductCard key={product.id} product={product} onQuickView={onQuickView} />
      ))}
    </div>
  )
}
