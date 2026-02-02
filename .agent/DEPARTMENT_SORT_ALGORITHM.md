# Department Sort Algorithm Explanation

## The Problem
When you dragged item D from position 3 to position 1, the frontend was sending `sort_number: 0` (the sort_number of the item at position 0), which was the same as the current sort_number of item at position 0, causing no update.

## The Solution

### Example: Moving Item D from position 3 to position 1

**Initial State:**
```
Position | ID | Name | sort_number
---------|----|----- |------------
    0    | 1  |  A   |     0
    1    | 2  |  B   |     1
    2    | 3  |  C   |     2
    3    | 4  |  D   |     3  ← Drag this
    4    | 5  |  E   |     4
```

**User Action:** Drag D to position 1 (between A and B)

**Frontend Calculation:**
```javascript
newPosition = 1 (row index in table)
currentPage = 0
perPage = 25
targetSortNumber = (0 * 25) + 1 = 1
```

**Backend Processing:**
```
Old sort_number: 3
New sort_number: 1
Direction: Moving UP (3 → 1)

Step 1: Shift items between positions 1 and 2 DOWN by 1
  - B (sort 1) → sort 2
  - C (sort 2) → sort 3

Step 2: Update dragged item
  - D (sort 3) → sort 1
```

**Final State:**
```
Position | ID | Name | sort_number
---------|----|----- |------------
    0    | 1  |  A   |     0
    1    | 4  |  D   |     1  ← Moved here
    2    | 2  |  B   |     2  ← Shifted down
    3    | 3  |  C   |     3  ← Shifted down
    4    | 5  |  E   |     4
```

## SQL Queries Generated

### Moving UP (position 3 → 1):
```sql
-- Shift items down
UPDATE departments 
SET sort_number = sort_number + 1 
WHERE sort_number BETWEEN 1 AND 2 
  AND id != 4;

-- Update dragged item
UPDATE departments 
SET sort_number = 1 
WHERE id = 4;
```

### Moving DOWN (position 1 → 3):
```sql
-- Shift items up
UPDATE departments 
SET sort_number = sort_number - 1 
WHERE sort_number BETWEEN 2 AND 3 
  AND id != 2;

-- Update dragged item
UPDATE departments 
SET sort_number = 3 
WHERE id = 2;
```

## Key Improvements

1. **Frontend**: Calculates absolute position accounting for pagination
2. **Backend**: Uses efficient bulk updates (increment/decrement)
3. **Transaction**: All updates wrapped in DB transaction
4. **Logging**: Detailed logs for debugging
5. **Performance**: Only updates affected rows, not all departments
