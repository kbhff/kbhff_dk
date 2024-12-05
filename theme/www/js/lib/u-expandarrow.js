// global function to add checkmark
u.addExpandArrow = function(node) {

	if(node.collapsearrow) {
		node.collapsearrow.parentNode.removeChild(node.collapsearrow);
		node.collapsearrow = false;
	}

	node.expandarrow = u.svg({
		"name":"expandarrow",
		"node":node,
		"class":"arrow",
		"width":17,
		"height":17,
		"shapes":[
			{
				"type": "line",
				"x1": 2,
				"y1": 2,
				"x2": 7,
				"y2": 9
			},
			{
				"type": "line",
				"x1": 6,
				"y1": 9,
				"x2": 11,
				"y2": 2
			}
		]
	});
}

u.addCollapseArrow = function(node) {

	if(node.expandarrow) {
		node.expandarrow.parentNode.removeChild(node.expandarrow);
		node.expandarrow = false;
	}

	node.collapsearrow = u.svg({
		"name":"collapsearrow",
		"node":node,
		"class":"arrow",
		"width":17,
		"height":17,
		"shapes":[
			{
				"type": "line",
				"x1": 2,
				"y1": 9,
				"x2": 7,
				"y2": 2
			},
			{
				"type": "line",
				"x1": 6,
				"y1": 2,
				"x2": 11,
				"y2": 9
			}
		]
	});
}

u.addPreviousArrow = function(node) {

	node.arrow = u.svg({
		"name":"prevearrow",
		"node":node,
		"class":"arrow",
		"width":17,
		"height":17,
		"shapes":[
			{
				"type": "line",
				"x1": 9,
				"y1": 2,
				"x2": 2,
				"y2": 7
			},
			{
				"type": "line",
				"x1": 2,
				"y1": 6,
				"x2": 9,
				"y2": 11
			}
		]
	});
}

u.addNextArrow = function(node) {

	node.arrow = u.svg({
		"name":"nextearrow",
		"node":node,
		"class":"arrow",
		"width":17,
		"height":17,
		"shapes":[
			{
				"type": "line",
				// "x1": 2,
				// "y1": 2,
				// "x2": 7,
				// "y2": 9
				"x1": 2,
				"y1": 2,
				"x2": 9,
				"y2": 7
			},
			{
				"type": "line",
				// "x1": 6,
				// "y1": 9,
				// "x2": 11,
				// "y2": 2
				"x1": 9,
				"y1": 6,
				"x2": 2,
				"y2": 11
			}
		]
	});
}
