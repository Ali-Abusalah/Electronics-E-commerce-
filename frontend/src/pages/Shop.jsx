import { useEffect, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import api from '../api/axios'
import Filters from '../components/Filters'
import ProductGrid from '../components/ProductGrid'
import QuickViewModal from '../components/QuickViewModal'

export default function Shop() {
  const [searchParams] = useSearchParams()
  const [categories, setCategories] = useState([])
  const [brands, setBrands] = useState([])
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [quickViewProduct, setQuickViewProduct] = useState(null)

  const [filters, setFilters] = useState({
    category: searchParams.get('category') || '',
    brands: [],
    maxPrice: 2000,
    inStock: false,
  })

  useEffect(() => {
    const loadMeta = async () => {
      try {
        const [cats, brs] = await Promise.all([
          api.get('/categories'),
          api.get('/brands'),
        ])
        setCategories(cats.data)
        setBrands(brs.data)
      } catch (err) {
        console.error('Failed to load filters meta:', err)
      }
    }
    loadMeta()
  }, [])

  useEffect(() => {
    const category = searchParams.get('category') || ''
    setFilters((prev) => (prev.category === category ? prev : { ...prev, category }))
  }, [searchParams])

  useEffect(() => {
    const category = searchParams.get('category') || ''
    const search = searchParams.get('search') || ''
    setLoading(true)
    setError('')

    const params = {}
    if (search) params.search = search
    if (category) params.category = category
    if (filters.brands.length) params.brand = filters.brands.join(',')
    if (filters.maxPrice < 2000) params.max_price = filters.maxPrice
    if (filters.inStock) params.in_stock = '1'

    api
      .get('/products', { params })
      .then(({ data }) => setProducts(data))
      .catch((err) => {
        console.error(err)
        setError('Failed to load products. Is the backend running?')
      })
      .finally(() => setLoading(false))
  }, [searchParams, filters])

  return (
    <div className="shop-layout">
      <Filters
        categories={categories}
        brands={brands}
        filters={filters}
        onChange={setFilters}
      />

      <main className="shop-main">
        <div className="hero-banner">
          <div className="hero-pill">NEW SEASON · NEW TECH</div>
          <h2 className="hero-headline">
            Gear up with the<br />
            <span className="hero-accent">latest electronics</span>
          </h2>
          <p className="hero-sub">
            TVs, smartphones, laptops and accessories — hand-picked deals delivered fast, backed by a 2-year warranty.
          </p>
          <div className="hero-actions">
            <Link to="/?category=TVs" className="hero-btn hero-btn-primary">Shop now</Link>
            <Link to="/" className="hero-btn hero-btn-outline">Browse deals</Link>
          </div>
          <div className="hero-perks">
            <span className="hero-perk">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                <rect x="1" y="3" width="15" height="13" rx="2" />
                <path d="M16 8h4l3 3v5h-7V8z" />
                <circle cx="5.5" cy="18.5" r="2.5" />
                <circle cx="18.5" cy="18.5" r="2.5" />
              </svg>
              Free shipping over $50
            </span>
            <span className="hero-perk">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
              2-year warranty
            </span>
            <span className="hero-perk">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
              </svg>
              24/7 support
            </span>
          </div>
        </div>

        <div className="shop-header">
          <h1>
            {filters.category ? filters.category : 'All Products'}
            {searchParams.get('search')
              ? ` — results for "${searchParams.get('search')}"`
              : ''}
          </h1>
          <span className="result-count">{products.length} products</span>
        </div>

        {error && <div className="error-banner">{error}</div>}

        {loading ? (
          <div className="loading-state">
            <div className="spinner" aria-hidden="true" />
            <p>Loading products...</p>
          </div>
        ) : (
          <ProductGrid products={products} onQuickView={setQuickViewProduct} />
        )}
      </main>

      {quickViewProduct && (
        <QuickViewModal
          product={quickViewProduct}
          onClose={() => setQuickViewProduct(null)}
        />
      )}
    </div>
  )
}