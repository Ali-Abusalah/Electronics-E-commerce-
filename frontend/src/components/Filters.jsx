export default function Filters({ categories, brands, filters, onChange }) {
  const maxPrice = filters.maxPrice

  const setCategory = (value) => onChange({ ...filters, category: value })

  const toggleBrand = (brand) => {
    const selected = new Set(filters.brands)
    if (selected.has(brand)) {
      selected.delete(brand)
    } else {
      selected.add(brand)
    }
    onChange({ ...filters, brands: [...selected] })
  }

  const setPrice = (value) => onChange({ ...filters, maxPrice: Number(value) })

  const toggleInStock = () =>
    onChange({ ...filters, inStock: !filters.inStock })

  const clearAll = () =>
    onChange({ category: '', brands: [], maxPrice: 2000, inStock: false })

  const hasActiveFilters =
    filters.category !== '' ||
    filters.brands.length > 0 ||
    filters.inStock

  return (
    <aside className="filters">
      <div className="filters-header">
        <h2>Filters</h2>
        {hasActiveFilters && (
          <button type="button" className="clear-filters" onClick={clearAll}>
            Clear all
          </button>
        )}
      </div>

      <div className="filter-group">
        <label className="filter-label" htmlFor="category-select">Category</label>
        <select
          id="category-select"
          className="filter-select"
          value={filters.category}
          onChange={(e) => setCategory(e.target.value)}
        >
          <option value="">All categories</option>
          {categories.map((cat) => (
            <option key={cat} value={cat}>{cat}</option>
          ))}
        </select>
      </div>

      <div className="filter-group">
        <span className="filter-label">Brand</span>
        <div className="brand-list">
          {brands.map((brand) => (
            <label key={brand} className="checkbox-label">
              <input
                type="checkbox"
                checked={filters.brands.includes(brand)}
                onChange={() => toggleBrand(brand)}
              />
              <span>{brand}</span>
            </label>
          ))}
        </div>
      </div>

      <div className="filter-group">
        <label className="filter-label" htmlFor="price-range">
          Max price: <strong>${maxPrice}</strong>
        </label>
        <input
          id="price-range"
          type="range"
          min="0"
          max="2000"
          step="50"
          value={maxPrice}
          onChange={(e) => setPrice(e.target.value)}
        />
        <div className="range-labels">
          <span>$0</span>
          <span>$2,000+</span>
        </div>
      </div>

      <div className="filter-group">
        <label className="checkbox-label">
          <input
            type="checkbox"
            checked={filters.inStock}
            onChange={toggleInStock}
          />
          <span>In stock only</span>
        </label>
      </div>
    </aside>
  )
}
