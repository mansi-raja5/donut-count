import React from 'react';

const Card = ({ title, actions, className, children }) => {
    return (
        <div className="bg-white rounded-lg overflow-hidden shadow-md">
            <div className="p-4">
                <div className={`flex justify-between ${!!title || !!actions ? 'mb-3' : ''}`}>
                    <div className="font-bold">{title}</div>
                    <div className="flex">{actions}</div>
                </div>
                <div className={className}>{children}</div>
            </div>
        </div>
    );
};

export default Card;